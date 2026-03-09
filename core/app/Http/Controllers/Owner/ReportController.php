<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingActionHistory;
use App\Models\NotificationLog;
use App\Models\PaymentLog;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction()
    {
        $pageTitle    = 'Transaction Logs';
        $transactions = Transaction::currentOwner()->searchable(['trx'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', 'desc')->paginate(getPaginate());
        return view('owner.reports.transactions', compact('pageTitle', 'transactions'));
    }

    public function emailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('owner.reports.email_details', compact('pageTitle', 'email'));
    }

    public function paymentsReceived()
    {
        return $this->getPaymentData('BOOKING_PAYMENT_RECEIVED', 'Received Payments History');
    }

    public function paymentReturned()
    {
        return $this->getPaymentData('BOOKING_PAYMENT_RETURNED', 'Returned Payments History');
    }

    public function bookingSituationHistory()
    {
        $pageTitle  = 'Booking Actions';
        $history    = BookingActionHistory::currentOwner();
        $remarks    = (clone $history)->groupBy('remark')->orderBy('remark')->get('remark');
        $bookingLog = (clone $history)->searchable(['booking:booking_number'])->filter(['remark'])->with('booking', 'actionBy')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('owner.reports.booking_actions', compact('pageTitle', 'bookingLog', 'remarks'));
    }

    public function bookings(Request $request)
    {
        $pageTitle = 'Bookings';
        $bookings = Booking::currentOwner();

        if ($request->booking_number) {
            $bookings = $bookings->where('booking_number', $request->booking_number);
        }

        if ($request->guest) {
            $bookings = $bookings->where(function ($q) use ($request) {
                $q->whereHas('user', function ($user) use ($request) {
                    $user->where('firstname', 'like', "%$request->guest%")->orWhere('lastname', 'like', "%$request->guest%")->orWhere(function ($user) use ($request) {
                        $user->whereRaw("CONCAT(firstname, ' ',lastname) LIKE?", ["%$request->guest%"]);
                    })->orWhere('email', 'like', "%$request->guest%");
                })->orWhereHas('guest', function ($guest) use ($request) {
                    $guest->where('name', 'like', "%$request->guest%")->orWhere('email', 'like', "%$request->email%");
                });
            });
        }

        if ($request->room_type_id) {
            $bookings = $bookings->whereHas('bookedRooms', function ($bookedRooms) use ($request) {
                $bookedRooms->where('room_type_id', $request->room_type_id);
            });
        }

        if ($request->room_number) {
            $bookings = $bookings->whereHas('bookedRooms', function ($bookedRooms) use ($request) {
                $bookedRooms->where('room_number', $request->room_number);
            });
        }

        $bookings = $bookings->customDateFilter()->customDateFilter('check_in')->customDateFilter('check_out');

        $insightBookings                = (clone $bookings)->get();
        $insights['total_bookings']     = $insightBookings->count();
        $insights['total_amount']       = $insightBookings->sum('total_amount');
        $insights['paid_amount'] = $insightBookings->sum('paid_amount');
        $insights['due_amount']  = $insightBookings->sum('due_amount');

        $bookings = $bookings->latest()->with('bookedRooms.roomType', 'user', 'guest')->paginate(getPaginate());
        $rooms = Room::currentOwner()->orderByRaw('room_number * 1 ASC')->get();
        $roomTypes = RoomType::currentOwner()->get();

        return view('owner.reports.bookings', compact('pageTitle', 'bookings', 'roomTypes', 'rooms', 'insights'));
    }

    protected function getPaymentData($type, $pageTitle)
    {
        $paymentLog = PaymentLog::currentOwner()->where('type', $type)->searchable(['booking:booking_number', 'booking.user:username'])->with('booking.user', 'actionBy')->orderBy('id', 'desc')->paginate(getPaginate());

        return view('owner.reports.payment_history', compact('pageTitle', 'paymentLog', 'pageTitle'));
    }
}
