<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookedRoom;
use App\Models\BookingActionHistory;
use App\Models\RoomType;
use App\Models\Room;

class BookingController extends Controller
{
    public function todaysBooked()
    {
        $pageTitle = request()->type == 'not_booked' ? 'Available Rooms to Book Today' : 'Todays Booked Rooms';

        $rooms = BookedRoom::whereHas('booking', function ($query) {
            $query->currentOwner();
        })->active()
            ->with([
                'room:id,room_number,room_type_id',
                'room.roomType:id,name',
                'booking:id,user_id,guest_id,booking_number',
                'booking.guest',
                'booking.user:id,firstname,lastname',
                'extraServices.extraService:id,name'
            ])
            ->where('booked_for', now()->toDateString())
            ->get();

        $disabledRoomTypeIDs = RoomType::currentOwner()->inactive()->pluck('id')->toArray();
        $bookedRooms         = $rooms->pluck('room_id')->toArray();
        $emptyRooms          = Room::currentOwner()->active()->whereNotIn('id', $bookedRooms)->whereNotIn('room_type_id', $disabledRoomTypeIDs)->with('roomType:id,name')->select('id', 'room_type_id', 'room_number')->get();

        return view('owner.booking.todays_booked', compact('pageTitle', 'rooms', 'emptyRooms'));
    }

    public function todayCheckInBooking()
    {
        $pageTitle = 'Today\'s Check In';
        $bookings = $this->bookingData('todayCheckIn');
        return view('owner.booking.list', compact('pageTitle', 'bookings'));
    }

    public function todayCheckoutBooking()
    {
        $pageTitle = 'Today\'s Checkout';
        $bookings = $this->bookingData('todayCheckout');
        return view('owner.booking.list', compact('pageTitle', 'bookings'));
    }

    public function activeBookings()
    {
        $pageTitle = 'Active Bookings';
        $bookings = $this->bookingData('active');
        return view('owner.booking.list', compact('pageTitle', 'bookings'));
    }

    public function checkedOutBookingList()
    {
        $pageTitle = 'Checked Out Bookings';
        $bookings = $this->bookingData('checkedOut');
        return view('owner.booking.list', compact('pageTitle', 'bookings'));
    }

    public function canceledBookingList()
    {
        $pageTitle = 'Canceled Bookings';
        $bookings = $this->bookingData('canceled');

        return view('owner.booking.list', compact('pageTitle', 'bookings'));
    }

    public function refundableBooking()
    {
        $pageTitle = 'Refundable Booking';
        $bookings = $this->bookingData('refundable');
        return view('owner.booking.list', compact('pageTitle', 'bookings'));
    }

    public function delayedCheckout()
    {
        $pageTitle = 'Delayed Checkout Bookings';
        $bookings = $this->bookingData('delayedCheckOut');
        return view('owner.booking.list', compact('pageTitle', 'bookings'));
    }

    public function allBookingList()
    {
        $pageTitle = 'All Bookings';
        $bookings = $this->bookingData('ALL');
        return view('owner.booking.list', compact('pageTitle', 'bookings'));
    }

    public function delayedCheckouts()
    {
        $pageTitle    = 'Delayed Checkouts';
        $bookings     = Booking::currentOwner()->delayedCheckout()->with('user', 'guest')->get();
        $emptyMessage = 'No delayed checkout found';
        $alertText    = 'The checkout periods for these bookings have passed, but the guests have not checked out yet.';
        return view('owner.booking.pending_checkin_checkout', compact('pageTitle', 'bookings', 'emptyMessage', 'alertText'));
    }

    public function pendingCheckIn()
    {
        $pageTitle         = 'Pending Check-Ins';
        $bookings          = Booking::currentOwner()->active()->keyNotGiven()->whereDate('check_in', '<=', now())->with('user', 'guest')->withCount('activeBookedRooms as total_room')->get();
        $emptyMessage      = 'No pending check-in found';
        $alertText         = 'The check-in periods for these bookings have passed, but the guests have not arrived yet.';
        return view('owner.booking.pending_checkin_checkout', compact('pageTitle', 'bookings', 'emptyMessage', 'alertText'));
    }

    public function upcomingCheckIn()
    {
        $pageTitle         = 'Upcoming Check-In Bookings';
        $bookings          = Booking::currentOwner()->active()->whereDate('check_in', '>', now())->whereDate('check_in', '<=', now()->addDays(hotelSetting('upcoming_checkin_days')))->with('user', 'guest')->withCount('activeBookedRooms as total_room')->orderBy('check_in')->get()->groupBy('check_in');
        $emptyMessage      = 'No upcoming check-in found';

        return view('owner.booking.upcoming_checkin_checkout', compact('pageTitle', 'bookings', 'emptyMessage'));
    }

    public function upcomingCheckout()
    {
        $pageTitle       = 'Upcoming Checkout Bookings';
        $bookings        = Booking::currentOwner()->active()->whereDate('check_out', '>', now())->whereDate('check_out', '<=', now()->addDays(hotelSetting('upcoming_checkout_days')))->with('user', 'guest')->withCount('activeBookedRooms as total_room')->orderBy('check_out')->get()->groupBy('check_out');
        $emptyMessage    = 'No upcoming checkout found';

        return view('owner.booking.upcoming_checkin_checkout', compact('pageTitle', 'bookings', 'emptyMessage'));
    }

    public function bookingDetails($id)
    {
        $booking = Booking::currentOwner()->with([
            'bookedRooms',
            'activeBookedRooms:id,booking_id,room_id',
            'activeBookedRooms.room:id,room_number',
            'bookedRooms.room:id,room_type_id,room_number',
            'bookedRooms.room.roomType:id,name',
            'usedExtraService.room',
            'usedExtraService.extraService',
            'payments',
            'guest'
        ])->findOrFail($id);

        $pageTitle = 'Booking Details';
        return view('owner.booking.details', compact('pageTitle', 'booking'));
    }

    public function bookedRooms($id)
    {
        $booking = Booking::currentOwner()->findOrFail($id);
        $pageTitle = 'Booked Rooms';
        $bookedRooms = BookedRoom::where('booking_id', $id)->with('booking.user', 'room.roomType')->orderBy('booked_for')->get()->groupBy('booked_for');
        return view('owner.booking.booked_rooms', compact('pageTitle', 'bookedRooms', 'booking'));
    }

    protected function bookingData($scope)
    {
        $query = Booking::currentOwner();

        if ($scope != "ALL") {
            $query = $query->$scope();
        }

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where(function ($bookings) use ($search) {
                $bookings->where('booking_number', $search)
                    ->orWhere(function ($bookings) use ($search) {
                        $bookings->whereHas('user', function ($user) use ($search) {
                            $user->where('username', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%");
                        })->orWhere(function ($bookings) use ($search) {
                            $bookings->whereHas('guest', function ($guest) use ($search) {
                                $guest->where('name', 'like', "%$search%")->orWhere('email', 'like', "%$search%");
                            });
                        });
                    });
            });
        }

        if ($request->check_in) {
            $query = $query->whereDate('check_in', $request->check_in);
        }

        if ($request->check_out) {
            $query = $query->whereDate('check_out', $request->check_out);
        }

        return $query->with('bookedRooms.room', 'user', 'activeBookedRooms', 'activeBookedRooms.room:id,room_number', 'guest')
            ->latest()
            ->orderBy('check_in', 'asc')
            ->paginate(getPaginate());
    }
}
