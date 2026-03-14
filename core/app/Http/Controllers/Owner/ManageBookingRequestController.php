<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\BookingRequestDetail;
use App\Models\PaymentSystem;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ManageBookingRequestController extends Controller {
    public function index() {
        $pageTitle       = 'All Booking Request';
        $bookingRequests = $this->bookingRequestData('initial');
        return view('owner.booking.request_list', compact('pageTitle', 'bookingRequests'));
    }

    public function cancel($id) {
        $bookingRequest         = BookingRequest::currentOwner()->initial()->with('bookingRequestDetails')->findOrFail($id);
        $bookingRequest->status = Status::BOOKING_REQUEST_CANCELED;
        $bookingRequest->save();

        notify($bookingRequest->user, 'BOOKING_REQUEST_CANCELED', [
            'number_of_rooms' => $bookingRequest->totalRoom(),
            'check_in'        => showDateTime($bookingRequest->check_in, 'd M, Y'),
            'check_out'       => showDateTime($bookingRequest->check_out, 'd M, Y'),
        ]);

        $notify[] = ['success', 'Booking request canceled successfully'];
        return to_route('owner.request.booking.all')->withNotify($notify);
    }

    public function deleteCanceledList() {
        $canceledIds = BookingRequest::currentOwner()->canceled()->pluck('id')->toArray();
        BookingRequestDetail::whereIn('booking_request_id', $canceledIds)->delete();
        BookingRequest::currentOwner()->canceled()->delete();

        $notify[] = ['success', 'Canceled booking request deleted successfully'];
        return back()->withNotify($notify);
    }

    public function approve(Request $request, $id) {
        $bookingRequest = BookingRequest::currentOwner()->initial()->with('user', 'bookingRequestDetails', 'bookingRequestDetails.roomType:id,name', 'owner.hotelSetting')->findOrFail($id);
        $paymentSystems = PaymentSystem::currentOwner()->active()->orderBy('name')->get();
        $pageTitle      = "Assign Room";

        $view = view('owner.partials.assign_requested_room', compact('bookingRequest'))->render();
        return view('owner.booking.request_approve', compact('pageTitle', 'view', 'bookingRequest', 'paymentSystems'));
    }

    public function assignRoom(Request $request) {
        $request->validate([
            'booking_request_id' => 'required|exists:booking_requests,id',
            'room'               => 'required|array',
            'paid_amount'        => 'nullable|numeric|gt:0',
            'payment_system_id'  => 'nullable',
        ], [
            'The payment system field is required',
        ]);

        $paymentSystem = null;
        if ($request->paid_amount > 0) {
            if (!$request->payment_system_id) {
                $notify[] = ['error', 'Payment System field is required'];
                return back()->withNotify($notify);
            }

            $paymentSystem = PaymentSystem::currentOwner()->active()->where('id', $request->payment_system_id)->first();
        }

        $bookingRequest = BookingRequest::currentOwner()->initial()->with('user', 'bookingRequestDetails')->findOrFail($request->booking_request_id);
        $this->bookingValidation($request, $bookingRequest);
        $user = $bookingRequest->user;

        $booking                 = new Booking();
        $booking->booking_number = getTrx();
        $booking->owner_id       = getOwnerParentId();
        $booking->user_id        = $user->id;
        $booking->check_in       = $bookingRequest->check_in;
        $booking->check_out      = $bookingRequest->check_out;
        $booking->contact_info   = $bookingRequest->contact_info;
        $booking->total_adult    = $bookingRequest->total_adult;
        $booking->total_child    = $bookingRequest->total_child;
        $booking->tax_charge     = $bookingRequest->taxCharge();
        $booking->total_discount = $bookingRequest->discountAmount();
        $booking->booking_fare   = $bookingRequest->bookingFare();
        $booking->paid_amount    = $request->paid_amount ?? 0;
        $booking->status         = Status::BOOKING_ACTIVE;
        $booking->save();

        $booking->createActionHistory('approve_booking_request');

        if ($request->paid_amount > 0) {
            $booking->createPaymentLog($request->paid_amount, 'BOOKING_PAYMENT_RECEIVED', @$paymentSystem->name);
        }

        $roomIds     = [];
        $bookingRoom = [];

        foreach ($request->room as $key => $room) {
            $data        = explode('-', $room);
            $roomId      = $data[0];
            $bookedFor   = $data[1];
            $bookedFor   = Carbon::parse($bookedFor)->format('Y-m-d');
            $room        = Room::with('roomType')->find($roomId);
            $requestItem = $bookingRequest->bookingRequestDetails->where('room_type_id', $room->room_type_id)->first();

            $bookingRoom[$key]['booking_id']       = $booking->id;
            $bookingRoom[$key]['room_type_id']     = $room->room_type_id;
            $bookingRoom[$key]['room_id']          = $room->id;
            $bookingRoom[$key]['room_number']      = $room->room_number;
            $bookingRoom[$key]['booked_for']       = $bookedFor;
            $bookingRoom[$key]['fare']             = $room->roomType->fare;
            $bookingRoom[$key]['discount']         = $requestItem->unitDiscount();
            $bookingRoom[$key]['tax_charge']       = $requestItem->taxCharge();
            $bookingRoom[$key]['cancellation_fee'] = $room->roomType->cancellation_fee;
            $bookingRoom[$key]['status']           = Status::ROOM_ACTIVE;
            $bookingRoom[$key]['created_at']       = now();
            $bookingRoom[$key]['updated_at']       = now();

            array_push($roomIds, $room->id);
        }

        BookedRoom::insert($bookingRoom);

// Instead of deleting the booking request, update its status
$bookingRequest->status = Status::BOOKING_REQUEST_APPROVED;
$bookingRequest->save();

        $roomNumbers = Room::whereIn('id', $roomIds)->pluck('room_number')->toArray();
        $rooms       = implode(" , ", $roomNumbers);

        notify($user, 'BOOKING_REQUEST_APPROVED', [
            'booking_number' => $booking->booking_number,
            'amount'         => showAmount($booking->total_amount, currencyFormat: false),
            'paid_amount'    => showAmount($booking->paid_amount, currencyFormat: false),
            'rooms'          => $rooms,
            'check_in'       => showDateTime($booking->check_in, 'd M, Y'),
            'check_out'      => showDateTime($booking->check_out, 'd M, Y'),
        ]);

        $notify[] = ['success', 'Booking Request approved successfully'];
        return to_route('owner.request.booking.all')->withNotify($notify);
    }

    private function bookingValidation($request, $bookingRequest) {
        $bookingCount      = [];
        $roomStatus        = true;
        $requestItemStatus = true;

        foreach ($request->room as $room) {
            $data   = explode('-', $room);
            $roomId = $data[0];

            $room = Room::active()->find($roomId);
            if (!$room) {
                $roomStatus = false;
                break;
            }

            $requestedItem = $bookingRequest->bookingRequestDetails->where('room_type_id', $room->room_type_id)->first();
            if (!$requestedItem) {
                $requestItemStatus = false;
                break;
            }

            $bookedFor                                              = $data[1];
            $bookedFor                                              = Carbon::parse($bookedFor)->format('Y-m-d');
            $bookingCount[$bookedFor][$requestedItem->room_type_id] = @$bookingCount[$bookedFor][$requestedItem->room_type_id] + 1;
        }

        if (!$roomStatus) {
            throw ValidationException::withMessages(['error' => 'Room not found']);
        }

        if (!$requestItemStatus) {
            throw ValidationException::withMessages(['error' => 'Invalid room type selected']);
        }

        $dates = array_keys($bookingCount);
        sort($bookingCount);

        if ($dates[0] != $bookingRequest->check_in) {
            throw ValidationException::withMessages(['error' => 'Check in date must be same as booking request checkin date']);
        }

        if (end($dates) != Carbon::parse($bookingRequest->check_out)->subDay()->format('Y-m-d')) {
            throw ValidationException::withMessages(['error' => 'Check out date must be same as booking request checkout date']);
        }

        foreach ($bookingCount[0] as $roomTypeId => $totalRoom) {
            $bookingRequestItem = $bookingRequest->bookingRequestDetails->where('room_type_id', $roomTypeId)->first();
            if ($totalRoom < $bookingRequestItem->number_of_rooms) {
                throw ValidationException::withMessages(['error' => 'You can\'t booked less than of request rooms!']);
            }
        }

        foreach (end($bookingCount) as $roomTypeId => $totalRoom) {
            $bookingRequestItem = $bookingRequest->bookingRequestDetails->where('room_type_id', $roomTypeId)->first();
            if ($totalRoom > $bookingRequestItem->number_of_rooms) {
                throw ValidationException::withMessages(['error' => 'You can\'t booked greater than of request rooms!']);
            }
        }

        if ($request->paid_amount > $bookingRequest->total_amount) {
            throw ValidationException::withMessages(['error' => 'Paid amount should be less than or equal to total amount']);
        }
    }

    protected function bookingRequestData($scope) {
        $query = BookingRequest::currentOwner()->$scope()->searchable(['user:username,email'])->with('user', 'bookingRequestDetails', 'bookingRequestDetails.roomType:id,name')->paginate(getPaginate());
        return $query;
    }
}
