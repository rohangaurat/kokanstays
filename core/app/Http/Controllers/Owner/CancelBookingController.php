<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookedRoom;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;

class CancelBookingController extends Controller
{

    public function cancelBooking($id)
    {
        $booking   = Booking::currentOwner()->active()->with('activeBookedRooms.room.roomType')->findOrFail($id);
        $pageTitle = 'Cancel Booking';
        return view('owner.booking.cancel', compact('pageTitle', 'booking'));
    }

    public function cancelFullBooking($id)
    {
        $booking     = Booking::currentOwner()->active()->findOrFail($id);
        $bookedRooms = BookedRoom::active()->where('booking_id', $booking->id);

        $booking->cancellation_fee += (clone $bookedRooms)->sum('cancellation_fee');
        $booking->booking_fare  -= (clone $bookedRooms)->sum('fare');
        $booking->tax_charge -= (clone $bookedRooms)->sum('tax_charge');

        $booking->status = Status::BOOKING_CANCELED;
        $booking->save();

        $roomIds =  $booking->bookedRooms()->pluck('room_id')->toArray();
        $rooms   = Room::currentOwner()->whereIn('id', $roomIds)->get()->pluck('room_number')->toArray();
        $bookedRooms->update(['status' => Status::ROOM_CANCELED]);

        notify($this->bookingGuest($booking), 'BOOKING_CANCELED', [
            'booking_number' => $booking->booking_number,
            'rooms'          => implode(', ', $rooms),
            'check_in'       => Carbon::parse($booking->bookedRooms->first()->booked_for)->format('d M, Y'),
            'check_out'      => Carbon::parse($booking->bookedRooms->last()->booked_for)->format('d M, Y')
        ]);

        $booking->createActionHistory('cancel_booking');

        $notify[] = ['success', 'Booking canceled successfully'];
        return to_route('owner.booking.all')->withNotify($notify);
    }

    public function cancelBookingByDate(Request $request, $id)
    {
        if ($request->booked_for < now()->toDateString()) {
            $notify[] = ['error', 'Past date\'s bookings can\'t be canceled'];
            return back()->withNotify($notify);
        }

        $booking  = Booking::currentOwner()->active()->find($id);

        if (!$booking) {
            $notify[] = ['error', 'This booking can\'t be canceled'];
            return back()->withNotify($notify);
        }

        $bookedRooms         = BookedRoom::active()->where('booking_id', $booking->id);
        $bookedForOtherDates = (clone $bookedRooms)->where('booked_for', '!=', $request->booked_for)->count();
        $bookedRooms         = (clone $bookedRooms)->whereDate('booked_for', $request->booked_for);

        $booking->cancellation_fee += (clone $bookedRooms)->sum('cancellation_fee');
        $booking->booking_fare  -= (clone $bookedRooms)->sum('fare');
        $booking->tax_charge -= (clone $bookedRooms)->sum('tax_charge');

        if (!$bookedForOtherDates) {
            $booking->status = Status::BOOKING_CANCELED;
        }

        $booking->save();

        $dateWiseBooked = (clone $bookedRooms)->get()->pluck('room_id')->toArray();
        $bookedRooms->update(['status' => Status::ROOM_CANCELED]);

        $this->updateCheckInCheckoutDate($booking);

        $rooms = Room::currentOwner()->whereIn('id', $dateWiseBooked)->get()->pluck('room_number')->toArray();

        notify($this->bookingGuest($booking), 'BOOKING_CANCELED_BY_DATE', [
            'booking_number' => $booking->booking_number,
            'date'           => showDateTime($request->booked_for, 'd M, Y'),
            'rooms'          => implode(', ', $rooms)
        ]);

        $booking->createActionHistory('cancel_booking');

        $notify[] = ['success', 'Booking canceled successfully'];
        return back()->withNotify($notify);
    }

    public function cancelSingleBookedRoom(Request $request, $id)
    {
        $bookedRoom = BookedRoom::whereHas('booking', function ($query) {
            $query->currentOwner();
        })->with('booking', 'room')->findOrFail($id);

        if ($bookedRoom->status != Status::ROOM_ACTIVE) {
            $notify[] = ['error', 'This room can\'t be canceled'];
        }

        if ($bookedRoom->booked_for < now()->toDateString()) {
            $notify[] = ['error', 'Previous days booking can\'t be canceled'];
            return back()->withNotify($notify);
        }

        $booking              = $bookedRoom->booking;
        $anotherBookedRooms   = BookedRoom::active()->whereHas('booking', function ($query) {
            $query->currentOwner();
        })->where('id', '!=', $request->id)->exists();

        $booking->cancellation_fee += $bookedRoom->cancellation_fee;
        $booking->booking_fare -= $bookedRoom->fare;
        $booking->tax_charge -= $bookedRoom->tax_charge;

        if (!$anotherBookedRooms) {
            $booking->status = Status::BOOKING_CANCELED;
        }

        $booking->save();

        $bookedRoom->status = Status::ROOM_CANCELED;
        $bookedRoom->save();

        $this->updateCheckInCheckoutDate($booking);

        notify($this->bookingGuest($booking), 'CANCEL_BOOKED_ROOM', [
            'booking_number' => $booking->booking_number,
            'date'           => showDateTime($bookedRoom->booked_for, 'd M, Y'),
            'room_number'    => @$bookedRoom->room->room_number
        ]);

        $notify[] = ['success', 'Room canceled successfully'];
        return back()->withNotify($notify);
    }

    private function bookingGuest($booking)
    {
        if ($booking->user) {
            return $booking->user;
        }

        $guest = new User();
        $guest->username = $booking->guest->name;
        $guest->fullname = $booking->guest->name;
        $guest->email    = $booking->guest->email;
        $guest->mobile   = $booking->guest->mobile;

        return $guest;
    }

    protected function updateCheckInCheckoutDate($booking)
    {
        $lastDateBookedRoom  = $booking->activeBookedRooms()->orderBy('booked_for', 'desc')->first();
        $firstDateBookedRoom = $booking->activeBookedRooms()->orderBy('booked_for', 'asc')->first();

        if ($lastDateBookedRoom) {
            $booking->check_out = Carbon::parse($lastDateBookedRoom->booked_for)->addDay()->format('Y-m-d');
        }

        if ($firstDateBookedRoom) {
            $booking->check_in = $firstDateBookedRoom->booked_for;
        }

        $booking->save();
    }
}
