<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Guest;
use App\Models\Owner;
use App\Models\PaymentSystem;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookRoomController extends Controller {
    public function room($id = null) {
        session()->forget('booking_info');
        $bookingRequest = BookingRequest::currentOwner()->initial()->find($id);
        $pageTitle      = 'Book Room';
        $roomTypes      = RoomType::currentOwner()->active()->get(['id', 'name']);
        $paymentSystems = PaymentSystem::currentOwner()->active()->get();
        return view('owner.booking.book', compact('pageTitle', 'roomTypes', 'paymentSystems', 'bookingRequest'));
    }

    function searchRoom(Request $request) {
        $validator = Validator::make($request->all(), [
            'room_type' => 'required|exists:room_types,id',
            'date'      => 'required|string',
            'rooms'     => 'required|integer|gt:0',
            'is_reset'  => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()->all(),
            ]);
        }

        $date = explode('-', $request->date);

        $request->merge([
            'checkin_date'  => trim(@$date[0]),
            'checkout_date' => trim(@$date[1]),
        ]);

        $validator = Validator::make($request->all(), [
            'checkin_date'  => 'required|date_format:m/d/Y|after:yesterday',
            'checkout_date' => 'required|date_format:m/d/Y|after:checkin_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()->all(),
            ]);
        }

        $bookingInfo = session()->get('booking_info') ?? collect([]);

        $firstInfo = $bookingInfo->first();
        if ($firstInfo && ($firstInfo['check_in'] != $request->checkin_date || $firstInfo['check_out'] != $request->checkout_date)) {
            return response()->json([
                'status' => false,
                'error'  => 'Check-In and checkout date should be same',
            ]);
        }

        if ($request->is_reset == 0 && $bookingInfo->firstWhere('room_type', $request->room_type) != null) {
            return response()->json([
                'status' => false,
                'error'  => 'Room Type already exist in the list',
            ]);
        }

        $response = json_decode($this->getRooms($request));

        if (!$response->status) {
            return response()->json([
                'status' => false,
                'error'  => $response->error,
            ]);
        }

        $data = [
            'check_in'        => $request->checkin_date,
            'check_out'       => $request->checkout_date,
            'room_type'       => $request->room_type,
            'number_of_rooms' => $request->rooms,
        ];

        $bookingInfo->put($request->room_type, $data);
        session()->put('booking_info', $bookingInfo);

        return response()->json([
            'status'    => true,
            'html'      => $response->html,
            'check_in'  => $request->checkin_date,
            'check_out' => $request->checkout_date,
        ]);
    }

    public function book(Request $request) {
        $paymentSystems = PaymentSystem::currentOwner()->active()->pluck('id')->toArray();
        $validator      = Validator::make($request->all(), [
            'guest_type'        => 'required|in:1,0',
            'guest_name'        => 'nullable|required_if:guest_type,0',
            'email'             => 'required|email',
            'mobile'            => 'nullable|required_if:guest_type,0|regex:/^([0-9]*)$/',
            'address'           => 'nullable|required_if:guest_type,0|string',
            'room'              => 'required|array',
            'paid_amount'       => 'nullable|numeric|gte:0',
            'total_adult'       => 'required|integer|gt:0',
            'total_child'       => 'nullable|integer|gte:0',
            'payment_system_id' => 'nullable|in:' . implode(',', $paymentSystems),
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $paymentSystem = null;
        if ($request->paid_amount > 0) {
            $paymentSystem = PaymentSystem::currentOwner()->active()->where('id', $request->payment_system_id)->first();
            if (!$paymentSystem) {
                return response()->json(['error' => 'Payment method field is required']);
            }
        }

        $guestId = 0;

        if ($request->guest_type == 1) {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'No registered guest found with this email']);
            }

            $contactInfo = [
                'name'  => $user->fullname,
                'phone' => $user->mobile,
            ];
        } else {
            $guestId = $this->insertGuestInfo($request);

            $contactInfo = [
                'name'  => $request->guest_name,
                'phone' => $request->mobile,
            ];
        }

        $parentOwner = $owner = authOwner()->load('hotelSetting');

        if ($owner->parent_id) {
            $parentOwner = Owner::where('id', $owner->parent_id)->with('hotelSetting')->first();
        }

        $bookedRoomData = [];
        $totalFare      = 0;
        $totalDiscount  = 0;
        $totalTax       = 0;
        $tax            = $parentOwner->hotelSetting->tax_percentage;

        $totalAdultCapacity = 0;
        $totalChildCapacity = 0;

        foreach ($request->room as $room) {
            $data      = [];
            $roomId    = explode('-', $room)[0];
            $bookedFor = explode('-', $room)[1];
            $isBooked  = BookedRoom::where('room_id', $roomId)->where('booked_for', $bookedFor)->exists();

            if ($isBooked) {
                return response()->json(['error' => 'Room has been booked']);
            }

            $room = Room::with('roomType')->find($roomId);

            $discountedFare = $room->roomType->fare - $room->discountAmount();
            $singleRoomTax  = $discountedFare * $tax / 100;

            $totalAdultCapacity += $room->roomType->total_adult;
            $totalChildCapacity += $room->roomType->total_child;

            $data['booking_id']       = 0;
            $data['room_type_id']     = $room->room_type_id;
            $data['room_id']          = $room->id;
            $data['room_number']      = $room->room_number;
            $data['booked_for']       = Carbon::parse($bookedFor)->format('Y-m-d');
            $data['fare']             = $room->roomType->fare;
            $data['discount']         = $room->discountAmount();
            $data['tax_charge']       = $singleRoomTax;
            $data['cancellation_fee'] = $room->roomType->cancellation_fee;
            $data['status']           = Status::ROOM_ACTIVE;
            $data['created_at']       = now();
            $data['updated_at']       = now();

            $bookedRoomData[] = $data;

            $totalDiscount += $room->discountAmount();
            $totalTax += $singleRoomTax;
            $totalFare += $room->roomType->fare;
        }

        $totalAmount = $totalFare + $totalTax - $totalDiscount;
        if ($request->paid_amount && $request->paid_amount > $totalAmount) {
            return response()->json(['error' => 'Paying amount can\'t be greater than total amount']);
        }

        $checkIn  = Carbon::parse($request->checkin_date);
        $checkOut = $request->checkout_date ? Carbon::parse($request->checkout_date) : $checkIn;

        $totalDay = $checkIn->diffInDays($checkOut);

        $totalAdultCapacity /= $totalDay;
        $totalChildCapacity /= $totalDay;

        if ($request->total_adult > $totalAdultCapacity || $request->total_child > $totalChildCapacity) {
            return response()->json(['error' => 'The total number of adults and children exceeds the capacity limit']);
        }

        $booking                 = new Booking();
        $booking->owner_id       = $parentOwner->id;
        $booking->booking_number = getTrx();
        $booking->user_id        = @$user->id ?? 0;
        $booking->guest_id       = $guestId;
        $booking->contact_info   = $contactInfo;
        $booking->total_adult    = $request->total_adult ?? 0;
        $booking->total_child    = $request->total_child ?? 0;
        $booking->total_discount = $totalDiscount;
        $booking->tax_charge     = $totalTax;
        $booking->booking_fare   = $totalFare;
        $booking->paid_amount    = $request->paid_amount ?? 0;
        $booking->status         = Status::BOOKING_ACTIVE;
        $booking->save();

        session()->forget('booking_info');

        if ($request->paid_amount > 0) {
            $booking->createPaymentLog($booking->paid_amount, 'BOOKING_PAYMENT_RECEIVED', @$paymentSystem->name);
        }
        $booking->createActionHistory('book_room');

        foreach ($bookedRoomData as $key => $bookedRoom) {
            $bookedRoomData[$key]['booking_id'] = $booking->id;
        }

        BookedRoom::insert($bookedRoomData);

        $checkIn  = BookedRoom::where('booking_id', $booking->id)->min('booked_for');
        $checkout = BookedRoom::where('booking_id', $booking->id)->max('booked_for');

        $booking->check_in  = $checkIn;
        $booking->check_out = Carbon::parse($checkout)->addDay()->toDateString();
        $booking->save();

        return response()->json(['success' => 'Room booked successfully']);
    }

    private function insertGuestInfo($request) {
        $guest          = new Guest();
        $guest->name    = $request->guest_name;
        $guest->email   = $request->email;
        $guest->mobile  = $request->mobile;
        $guest->address = $request->address;
        $guest->save();

        return $guest->id;
    }

    public function updateRoomSessionData(Request $request) {
        $validator = Validator::make($request->all(), [
            'room_type' => 'required|exists:room_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()->all(),
            ]);
        }

        $bookingInfo = session()->get('booking_info');
        $bookingInfo->forget($request->room_type);
        session()->put('booking_info', $bookingInfo);

        return response()->json([
            'status'  => true,
            'message' => 'Booking session data updated successfully',
        ]);
    }

    public function getRooms(Request $request) {
        $checkIn  = Carbon::parse($request->checkin_date);
        $checkOut = $request->checkout_date ? Carbon::parse($request->checkout_date) : $checkIn;

        $roomType = RoomType::active()->withCount(['rooms as total_rooms' => function ($q) {
            $q->active();
        }])
            ->addSelect(['booked_rooms' => function ($subQuery) use ($request, $checkIn, $checkOut) {
                $subQuery->selectRaw('COUNT(DISTINCT room_id)')
                    ->from('booked_rooms')
                    ->join('rooms', 'booked_rooms.room_id', 'rooms.id')
                    ->where('rooms.status', Status::ENABLE)
                    ->where('booked_rooms.status', Status::ROOM_ACTIVE)
                    ->whereBetween('booked_for', [$checkIn, $checkOut])
                    ->whereColumn('booked_rooms.room_type_id', 'room_types.id');
            }])
            ->selectRaw('(SELECT total_rooms - booked_rooms) as available_rooms')->find($request->room_type);

        if (!$roomType) {
            return json_encode([
                "status" => false,
                'error'  => 'Room Type not found',
            ]);
        }

        if ($request->get_available) {
            return response()->json([
                'status'          => true,
                'available_rooms' => $roomType->available_rooms,
            ]);
        }

        $rooms = Room::active()
            ->where('room_type_id', $roomType->id)
            ->with([
                'booked' => function ($q) {
                    $q->active();
                },
                'roomType:id,name,fare,discount_percentage',
            ])
            ->get();

        if ($roomType->available_rooms < $request->rooms) {
            return json_encode([
                "status" => false,
                'error'  => 'The requested number of rooms is not available for the selected date',
            ]);
        }

        $numberOfRooms = $request->rooms;

        return json_encode([
            "status" => true,
            'html'   => view('partials.rooms', compact('checkIn', 'checkOut', 'rooms', 'numberOfRooms', 'roomType'))->render(),
        ]);
    }
}
