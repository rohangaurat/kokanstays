<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\Intended;
use App\Models\Amenity;
use App\Models\BedType;
use App\Models\City;
use App\Models\Facility;
use App\Models\Owner;
use App\Models\Review;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller {
    private function filterHotels($hotels) {
        if (request('popular') == 'true') {
            $hotels = $hotels->withCount([
                'bookings as total_bookings' => function ($query) {
                    $query->where('status', Status::BOOKING_ACTIVE)->whereDate('created_at', '>=', Carbon::now()->subDays(gs('popularity_count_from')));
                },
            ])->orderByDesc('total_bookings');
        }
        if (request('featured') == 'true') {
            $hotels = $hotels->having('is_featured', Status::YES);
        }
        if (request('min_fare') && request('max_fare')) {
            $hotels = $hotels->having('minimum_fare', '>=', request('min_fare'))->having('maximum_fare', '<=', request('max_fare'));
        }
        if (request('city_id')) {
            $hotels = $hotels->whereHas('hotelSetting', function ($hotelSetting) {
                $hotelSetting->where('city_id', request('city_id'));
            });
        }
        if (request('user_rating')) {
            $minRating = min(request('user_rating'));
            $hotels    = $hotels->whereHas('hotelSetting', function ($hotelSetting) use ($minRating) {
                $hotelSetting->where('avg_rating', '>=', $minRating);
            });
        }
        if (request('hotel_star')) {
            $hotels = $hotels->where('star_rating', request('hotel_star'));
        }
        if (request('bed_type')) {
            $hotels = $hotels->whereHas('roomTypes', function ($roomTypes) {
                $roomTypes->where(function ($subQuery) {
                    foreach (request('bed_type') as $bed) {
                        $subQuery->orWhereRaw('LOWER(beds) LIKE ?', ['%' . strtolower($bed) . '%']);
                    }
                });
            });
        }
        if (request('facilities')) {
            $hotels = $hotels->whereHas('hotelSetting', function ($hotelSetting) {
                $hotelSetting->whereHas('facilities', function ($facilities) {
                    $facilities->whereIn('facility_id', request('facilities'));
                });
            });
        }
        if (request('amenities')) {
            $hotels = $hotels->whereHas('hotelSetting', function ($hotelSetting) {
                $hotelSetting->whereHas('amenities', function ($amenities) {
                    $amenities->whereIn('amenities_id', request('amenities'));
                });
            });
        }
        return $hotels;
    }

    public function hotels() {
        if (request('rooms')) {
            $values  = explode(',', request('rooms'));
            $grouped = array_chunk($values, 3);
        }

        if (session()->has('popular_hotels')) {
            request()->merge(['popular' => 'true']);
        }

        if (session()->has('featured_hotels')) {
            request()->merge(['featured' => 'true']);
        }

        $totalAdult = request('rooms') ? array_sum(array_column($grouped, 1)) : 2;
        $totalChild = request('rooms') ? array_sum(array_column($grouped, 2)) : 0;
        $checkIn    = request('check_in') ? Carbon::parse(request('check_in')) : Carbon::today();

        if (request('check_out') == 'null') {
            request()->merge(['check_out' => $checkIn->addDays(30)]);
        }
        $checkout = Carbon::parse(request('check_out'));

        $hotels = Owner::select('owners.*', 'countries.name as country', 'cities.name as city')
            ->selectRaw('SUM(valid_rooms.available_rooms) as available_rooms')
            ->selectRaw('SUM(room_types.total_adult * valid_rooms.available_rooms) as adult_capacity')
            ->selectRaw('SUM(room_types.total_child * valid_rooms.available_rooms) as child_capacity')
            ->selectRaw('MIN(room_types.fare) as minimum_fare')
            ->selectRaw('MAX(room_types.fare) as maximum_fare')
            ->join('room_types', 'owners.id', '=', 'room_types.owner_id')
            ->join('hotel_settings', 'owners.id', '=', 'hotel_settings.owner_id')
            ->join('cities', 'hotel_settings.city_id', '=', 'cities.id')
            ->join('countries', 'cities.country_id', '=', 'countries.id')
            ->joinSub(function ($query) use ($checkIn, $checkout) {
                $query->select('rooms.room_type_id', DB::raw('COUNT(rooms.id) as available_rooms'))
                    ->from('rooms')
                    ->where('rooms.status', Status::ROOM_ACTIVE)
                    ->whereNotIn('rooms.id', function ($subQuery) use ($checkIn, $checkout) {
                        $subQuery->select('room_id')
                            ->from('booked_rooms')
                            ->whereBetween('booked_for', [$checkIn, $checkout])
                            ->where('status', Status::BOOKING_ACTIVE);
                    })
                    ->groupBy('rooms.room_type_id');
            }, 'valid_rooms', 'room_types.id', '=', 'valid_rooms.room_type_id')
            ->groupBy('owners.id')
            ->where('room_types.status', Status::ROOM_TYPE_ACTIVE)
            ->having('adult_capacity', '>=', $totalAdult)
            ->having('child_capacity', '>=', $totalChild)
            ->whereDate('owners.expire_at', '>=', now())
            ->where('owners.status', Status::USER_ACTIVE)
            ->totalReviews();

        $hotels      = $this->filterHotels($hotels);
        $totalHotels = (clone $hotels)->count();

        if (($totalHotels <= getPaginate()) && request('page') > 1) {
            $request = request();
            $page    = $request->merge(['page' => 1]);
            $page    = $request->page;
            $hotels  = $hotels->paginate(getPaginate() * $request->get('page'));
            collect($request->all())->except('page');
        } else {
            $hotels = $hotels->paginate(getPaginate());
        }

        if (request('filter')) {
            $view = view('Template::partials.hotel_list_card', compact('hotels'))->render();
            return response()->json([
                'view' => $view,
                'page' => $page ?? null,
            ]);
        }

        $pageTitle = 'Hotels';
        $cities    = City::active()
            ->with(['country' => function ($country) {
                $country->active();
            }])->get();
        $bedTypes   = BedType::all();
        $facilities = Facility::active()->get();
        $amenities  = Amenity::active()->get();

        return view('Template::hotels', compact('pageTitle', 'hotels', 'cities', 'bedTypes', 'facilities', 'amenities'));
    }

    public function details($id) {
        if (request('rooms')) {
            $values  = explode(',', request('rooms'));
            $grouped = array_chunk($values, 3);
        }

        $adultCounts = request('rooms') ? array_column($grouped, 1) : [2];
        $childCounts = request('rooms') ? array_column($grouped, 2) : [0];
        $checkIn     = request('check_in') ? Carbon::parse(request('check_in')) : Carbon::today();
        if (request('check_out') == 'null') {
            request()->merge(['check_out' => $checkIn->addDays(30)]);
        }
        $checkout = Carbon::parse(request('check_out'));
        $hotel    = Owner::active()->notExpired()->with(['hotelSetting'])->totalReviews()->findOrFail($id);

        $roomTypes = RoomType::active()
            ->where('owner_id', $hotel->id)
            ->with(['rooms' => function ($query) use ($checkIn, $checkout) {
                $query->active()->isAvailableRoom($checkIn, $checkout);
            }])
            ->get()
            ->filter(function ($roomType) use ($adultCounts, $childCounts, $checkIn, $checkout) {
                $availableRoomCount = $roomType->rooms()->availableRoomCount($checkIn, $checkout);
                $matchCount         = 0;
                foreach ($adultCounts as $index => $adultCount) {
                    $childCount = $childCounts[$index] ?? 0;
                    if ($roomType->total_adult >= $adultCount && $roomType->total_child >= $childCount) {
                        $matchCount++;
                    }
                }
                return $availableRoomCount && ($matchCount > 0);
            })
            ->values();

        if (request('filter')) {
            $hotelTaxPercentage = $hotel->hotelSetting->tax_percentage;
            $view               = view('Template::partials.room_type_card', compact('roomTypes', 'hotelTaxPercentage', 'checkIn', 'checkout'))->render();
            return response()->json([
                'view' => $view,
            ]);
        }

        $reviews   = Review::reviews()->where('owner_id', $hotel->id)->with('replies', 'user')->orderByDesc('id')->get();
        $pageTitle = 'Hotel details';
        return view('Template::hotel_detail', compact('pageTitle', 'hotel', 'reviews', 'roomTypes', 'checkIn', 'checkout'));
    }

    protected function bookingPreviewValidation($roomTypes, $id, $checkIn, $checkOut) {
        if (!$roomTypes) {
            $notify[] = ['error', 'Select room first.'];
            return ['error' => true, 'notify' => $notify];
        }
        foreach ($roomTypes as $key => $value) {
            $roomType = RoomType::where('owner_id', $id)->find($key);
            if (!$roomType) {
                $notify[] = ['error', 'Invalid room type selected.'];
                return ['error' => true, 'notify' => $notify];
            }
            $availableRoomCount = $roomType->rooms()->availableRoomCount($checkIn, $checkOut);
            if ($availableRoomCount <= 0) {
                $notify[] = ['error', 'Selected room type is not available.'];
                return ['error' => true, 'notify' => $notify];
            }
            if ($value <= 0) {
                $notify[] = ['error', 'Invalid quantity selected for room type.'];
                return ['error' => true, 'notify' => $notify];
            }
            if (($availableRoomCount - $value) < 0) {
                $notify[] = ['error', 'Selected room type is not available in the requested quantity.'];
                return ['error' => true, 'notify' => $notify];
            }
        }
        return ['error' => false];
    }

    public function bookingPreviewRequest(Request $request) {
        if (!auth()->check()) {
            Intended::identifyRoute();
            $notify[] = ['info', 'You need to login first.'];
            return to_route('user.login')->withNotify($notify);
        }

        if (session()->has('intended_info')) {
            request()->merge(
                session()->get('intended_info')['form_data'],
            );
            session()->forget('intended_info');
        }

        $request->validate([
            'room_type_ids' => 'required|json',
        ]);

        $roomTypes  = json_decode($request->input('room_type_ids'), true);
        $validation = $this->bookingPreviewValidation($roomTypes, $request->owner_id, $request->checkin, $request->checkout);
        if ($validation['error']) {
            return back()->withNotify($validation['notify']);
        }

        $roomTypeIds = array_keys($roomTypes);
        $hotel       = Owner::active()->notExpired()->where('id', $request->owner_id)
            ->with(['hotelSetting', 'roomTypes' => function ($roomTypeQuery) use ($roomTypeIds, $request) {
                $roomTypeQuery->active()->whereIn('id', $roomTypeIds)
                    ->whereHas('rooms', function ($rooms) use ($request) {
                        $rooms->isAvailableRoom($request->checkin, $request->checkout);
                    });
            }, 'roomTypes.facilities'])
            ->first();

        $data = [
            'hotel'      => $hotel,
            'roomTypes'  => $roomTypes,
            'checkin'    => $request->checkin,
            'checkout'   => $request->checkout,
            'totalGuest' => ($request->total_adult + $request->total_child),
            'totalAdult' => $request->total_adult,
            'totalChild' => $request->total_child,
        ];

        session()->put('preview_data', $data);
        return to_route('hotel.booking.preview', $request->owner_id);
    }

    public function bookingPreview($id) {
        if (!session()->has('preview_data')) {
            return to_route('hotel.details', $id);
        }
        $pageTitle   = 'Booking Preview';
        $previewData = session()->get('preview_data');
        $hotel       = $previewData['hotel'];
        $roomTypes   = $previewData['roomTypes'];
        $info        = json_decode(json_encode(getIpInfo()), true);
        $mobileCode  = isset($info['code']) ? implode(',', $info['code']) : '';
        $countries   = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::booking_preview', compact('pageTitle', 'roomTypes', 'hotel', 'mobileCode', 'countries', 'previewData'));
    }

    public function popularHotels() {
        session()->put('popular_hotels', true);
        return to_route('hotel.index');
    }

    public function featuredHotels() {
        session()->put('featured_hotels', true);
        return to_route('hotel.index');
    }
}
