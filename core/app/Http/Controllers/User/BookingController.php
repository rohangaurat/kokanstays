<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\BookingRequestDetail;
use App\Models\Owner;
use App\Models\OwnerNotification;
use App\Models\Review;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;

class BookingController extends Controller {
    public function bookingHistory() {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('bookedRooms', 'bookedRooms.room.roomType:id,name', 'owner.hotelSetting')
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $pageTitle = 'Booking History';
        return view('Template::user.booking.booking_history', compact('pageTitle', 'bookings'));
    }

    public function bookingRequestSubmit(Request $request) {
        $this->validation($request);

        $user  = auth()->user();
        $owner = Owner::active()->notExpired()->with('hotelSetting')->find($request->owner_id);

        $requestList        = [];
        $totalAmount        = 0;
        $totalAdultCapacity = 0;
        $totalChildCapacity = 0;
        $checkIn            = Carbon::parse($request->checkin);
        $checkout           = Carbon::parse($request->checkout);
        $stayingDays        = diffInDays($checkIn, $checkout);

        foreach ($request->room_types as $roomTypeId => $roomCount) {
            $roomType = RoomType::active()
                ->where('id', $roomTypeId)
                ->where('owner_id', $request->owner_id)
                ->with(['rooms' => function ($rooms) use ($request) {
                    $rooms->isAvailableRoom($request->checkin, $request->checkout);
                }])
                ->first();

            if (!$roomType) {
                $notify[] = ['error', 'Room type not found'];
                return back()->withNotify($notify);
            }
            if (count($roomType->rooms) < $roomCount) {
                $notify[] = ['error', 'Selected room type is not available in the requested quantity'];
                return back()->withNotify($notify);
            }

            $totalAdultCapacity += $roomType->total_adult * $roomCount;
            $totalChildCapacity += $roomType->total_child * $roomCount;

            $discount = $roomType->discount * $roomCount * $stayingDays;
            $amount   = $roomType->discounted_fare * $roomCount * $stayingDays;
            $totalTax = ($roomType->discounted_fare * $owner->hotelSetting->tax_percentage / 100) * $stayingDays * $roomCount;
            $subTotal = $amount + $totalTax;
            $totalAmount += $subTotal;

            $requestList[] = [
                'booking_request_id' => 0,
                'room_type_id'       => $roomTypeId,
                'number_of_rooms'    => $roomCount,
                'unit_fare'          => $roomType->fare,
                'tax_charge'         => $totalTax,
                'discount'           => $discount,
                'total_amount'       => $subTotal,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        if ($totalAdultCapacity < $request->total_adult || $totalChildCapacity < $request->total_child) {
            $notify[] = ['error', 'Selected room type is not available in the requested quantity'];
            return back()->withNotify($notify);
        }

        $contactInfo = [
            'name'  => $request->guest_name,
            'phone' => $request->dial_code . $request->contact_number,
        ];

        $bookingRequest               = new BookingRequest();
        $bookingRequest->owner_id     = $owner->id;
        $bookingRequest->user_id      = $user->id;
        $bookingRequest->total_adult  = $request->total_adult;
        $bookingRequest->total_child  = $request->total_child;
        $bookingRequest->check_in     = $checkIn->format('Y-m-d');
        $bookingRequest->check_out    = $checkout->format('Y-m-d');
        $bookingRequest->total_amount = $totalAmount;
        $bookingRequest->contact_info = $contactInfo;
        $bookingRequest->save();

        $requestId = $bookingRequest->id;
        array_walk($requestList, function (&$item) use ($requestId) {
            $item['booking_request_id'] = $requestId;
        });

        BookingRequestDetail::insert($requestList);

        $ownerNotification            = new OwnerNotification();
        $ownerNotification->owner_id  = $owner->id;
        $ownerNotification->user_id   = $user->id;
        $ownerNotification->title     = 'Booking request send by ' . $user->fullname;
        $ownerNotification->click_url = urlPath('owner.request.booking.approve', $bookingRequest->id);
        $ownerNotification->save();

        $notify[] = 'Booking request send successfully';
        return to_route('user.booking.request.history')->withNotify($notify);
    }

    private function validation($request) {
        $activeOwnerIDs = Owner::active()
            ->whereHas('hotelSetting')
            ->whereHas('roomTypes', function ($query) {
                $query->where('status', Status::ROOM_TYPE_ACTIVE)->whereDate('owners.expire_at', '>=', now());
            })
            ->pluck('id')
            ->toArray();

        $countryData = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $mobileCodes = implode(',', array_column($countryData, 'dial_code'));

        return $request->validate([
            'checkin'        => 'required|date_format:Y-m-d|after:yesterday',
            'checkout'       => 'required|date_format:Y-m-d|after:check_in',
            'owner_id'       => 'required|in:' . implode(',', $activeOwnerIDs),
            'room_types'     => 'required|array',
            'room_types.*'   => 'integer|exists:room_types,id',
            'total_adult'    => 'required|integer|gt:0',
            'total_child'    => 'required|integer|gte:0',
            'guest_name'     => 'required|string',
            'dial_code'      => 'required|in:' . $mobileCodes,
            'contact_number' => 'required',
        ]);
    }

    public function bookingRequestHistory() {
        $user            = auth()->user();
        $bookingRequests = BookingRequest::where('user_id', $user->id)->where('status', '!=', Status::BOOKING_REQUEST_APPROVED)
            ->with('owner.hotelSetting', 'bookingRequestDetails.roomType:id,name')
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $pageTitle = 'All Booking Request';
        return view('Template::user.booking.booking_request_history', compact('pageTitle', 'bookingRequests', 'user'));
    }

    public function bookingDetails($id) {
        $user    = auth()->user();
        $booking = Booking::where('user_id', $user->id)
            ->with(['bookedRooms', 'owner.hotelSetting'])
            ->findOrFail($id);

        $bookedRoomsIds = $booking->bookedRooms->pluck('id')->toArray();

        $roomTypes = RoomType::whereHas('bookedRooms', function ($bookedRoom) use ($bookedRoomsIds) {
            $bookedRoom->whereIn('id', $bookedRoomsIds);
        })->with('rooms')->get();

        $pageTitle      = 'Booking Details';
        $authUserReview = Review::reviews()->where('booking_id', $id)->first();
        return view('Template::user.booking.booking_details', compact('pageTitle', 'booking', 'roomTypes', 'authUserReview'));
    }

    public function bookingRequestDetails($id) {
        $booking   = BookingRequest::where('user_id', auth()->id())->with(['bookingRequestDetails.roomType'])->findOrFail($id);
        $pageTitle = 'Booking Request Details';
        return view('Template::user.booking.booking_request_details', compact('pageTitle', 'booking'));
    }

    public function bookingInvoice($id) {

    $booking = Booking::where('user_id', auth()->id())
        ->with([
            'bookedRooms',
            'bookedRooms.room.roomType',
            'usedExtraService.room',
            'usedExtraService.extraService',
            'payments', // VERY IMPORTANT
            'owner.hotelSetting',
            'user',
            'guest'
        ])
        ->findOrFail($id);

    $data = ['booking' => $booking];

    $pdf = PDF::loadView('partials.invoice', $data);

    return $pdf->stream($booking->booking_number . '.pdf');
}

    public function bookingDelete(Request $request, $id) {
        $bookingRequest = BookingRequest::initial()->findOrFail($id);
        BookingRequestDetail::where('booking_request_id', $bookingRequest->id)->delete();
        $bookingRequest->delete();

        $notify[] = ['success', 'Booking request canceled successfully'];
        return back()->withNotify($notify);
    }
}
