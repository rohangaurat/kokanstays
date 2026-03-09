<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\BookingRequestDetail;
use App\Models\Owner;
use App\Models\OwnerNotification;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingRequestController extends Controller {
    public function history() {
        $bookingRequests = BookingRequest::initial()->where('user_id', auth()->id())->with('owner', 'owner.hotelSetting:id,owner_id,name,image', 'bookingRequestDetails', 'bookingRequestDetails.roomType:id,name')->get();

        $notify[] = 'Booking request history';

        return responseSuccess('booking_request_history', $notify, [
            'booking_requests' => $bookingRequests,
        ]);
    }

    public function delete($id) {
        $bookingRequest = BookingRequest::initial()->where('id', $id)->first();

        if (!$bookingRequest) {
            $notify[] = 'Booking request not found';
            return responseError('validation_error', $notify);
        }

        BookingRequestDetail::where('booking_request_id', $bookingRequest->id)->delete();
        $bookingRequest->delete();

        $notify[] = 'Booking request canceled successfully';
        return responseSuccess('booking_request_history', $notify);
    }

    public function sendRequest(Request $request) {
        $validator = $this->validation($request);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $duplicatedValueTypes = array_filter(array_count_values(array_column($request->room_types, 'type_id')), function ($value) {
            return $value > 1;
        });

        if (count($duplicatedValueTypes)) {
            $notify[] = 'Duplicate room type found';
            return responseError('validation_error', $notify);
        }

        $requestList = [];
        $totalAmount = 0;

        $user  = auth()->user();
        $owner = Owner::with('hotelSetting')->find($request->owner_id);

        $checkIn            = Carbon::parse($request->check_in)->startOfDay();
        $checkout           = Carbon::parse($request->checkout)->startOfDay();
        $stayingDays        = (int) $checkIn->diffInDays($checkout);
        $totalAdultCapacity = 0;
        $totalChildCapacity = 0;

        foreach ($request->room_types as $requestedType) {
            $roomType = RoomType::active()
                ->whereHas('owner', function ($owner) {
                    $owner->active()->whereDate('expire_at', '>=', now());
                })
                ->withCount(['rooms as total_rooms' => function ($q) {
                    $q->active();
                }])
                ->addSelect(['booked_rooms' => function ($subQuery) use ($checkIn, $checkout) {
                    $subQuery->selectRaw('COUNT(DISTINCT room_id)')
                        ->from('booked_rooms')
                        ->join('rooms', 'booked_rooms.room_id', 'rooms.id')
                        ->where('rooms.status', Status::ENABLE)
                        ->where('booked_rooms.status', Status::ROOM_ACTIVE)
                        ->whereBetween('booked_for', [$checkIn, $checkout])
                        ->whereColumn('booked_rooms.room_type_id', 'room_types.id');
                }])
                ->selectRaw('(SELECT total_rooms - booked_rooms) as available_rooms')
                ->having('available_rooms', '>=', $requestedType['total_room'])
                ->where('owner_id', $owner->id)
                ->where('id', $requestedType['type_id'])->first();

            if (!$roomType) {
                $notify[] = 'The requested room quantity exceeds the available rooms';
                return response()->json([
                    'remark'  => 'validation_error',
                    'status'  => 'error',
                    'message' => ['error' => $notify],
                ]);
            }

            $totalRoom = $requestedType['total_room'];
            $totalAdultCapacity += $roomType->total_adult * $totalRoom;
            $totalChildCapacity += $roomType->total_child * $totalRoom;

            //discount calc
            $discount = $roomType->discount * $totalRoom * $stayingDays;

            //tax calc
            $tax      = $roomType->discounted_fare * $owner->hotelSetting->tax_percentage / 100;
            $totalTax = $tax * $totalRoom * $stayingDays;

            //total fare calc
            $totalFare = $roomType->discounted_fare * $totalRoom * $stayingDays;
            $subTotal  = $totalFare + $totalTax;
            $totalAmount += $subTotal;

            $requestList[] = [
                'booking_request_id' => 0,
                'room_type_id'       => $roomType->id,
                'number_of_rooms'    => $totalRoom,
                'unit_fare'          => $roomType->fare,
                'tax_charge'         => $totalTax,
                'discount'           => $discount,
                'total_amount'       => $subTotal,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        if ($totalAdultCapacity < $request->total_adult || $totalChildCapacity < $request->total_child) {
            $notify[] = 'The total number of adults or children exceeds the capacity limit';
            return responseError('validation_error', $notify);
        }

        $contactInfo = [
            'name'  => $request->contact_name,
            'phone' => $request->contact_number,
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
        return responseSuccess('booking_request', $notify);
    }

    private function validation($request) {
        $activeOwnerIDs = Owner::active()->whereHas('hotelSetting')->whereHas('roomTypes', function ($query) {
            $query->where('status', Status::ROOM_TYPE_ACTIVE)->whereDate('owners.expire_at', '>=', now());
        })->pluck('id')->toArray();

        $roomTypeIDs = RoomType::active()->where('owner_id', $request->owner_id)->pluck('id')->toArray();

        $rules = [
            'checkin'                 => 'required|date_format:Y-m-d|after:yesterday',
            'checkout'                => 'required|date_format:Y-m-d|after:check_in',
            'owner_id'                => 'required|in:' . implode(',', $activeOwnerIDs),
            'room_types'              => 'required|array',
            'room_types.*.type_id'    => 'required|in:' . implode(',', $roomTypeIDs),
            'room_types.*.total_room' => 'required|integer|gt:0',
            'total_adult'             => 'required|integer|gt:0',
            'total_child'             => 'required|integer|gte:0',
            'contact_name'            => 'required|string',
            'contact_number'          => 'required',
        ];

        $messages = [
            'owner_id.required'                => 'The owner field is required',
            'owner_id.in'                      => 'Invalid owner selected',
            'owner_id.gt'                      => 'The owner field must be greater than zero',
            'room_types.*.type_id.required'    => 'The room is required',
            'room_types.*.type_id.in'          => 'Room selection is invalid',
            'room_types.*.type_id.gt'          => 'Room must be greater than zero',
            'room_types.*.total_room.required' => 'A number of rooms is required',
            'room_types.*.total_room.integer'  => 'The number of rooms selected is invalid',
            'room_types.*.total_room.gt'       => 'The number of rooms should be greater than zero',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        return $validator;
    }
}
