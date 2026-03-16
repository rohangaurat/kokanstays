<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingActionHistory;
use App\Models\Deposit;
use App\Models\PaymentLog;
use App\Models\UsedExtraService;
use Illuminate\Support\Carbon;
use PDF;

class ManageBookingController extends Controller
{
    public function handoverKey($id)
    {
        $booking = Booking::currentOwner()->active()->findOrFail($id);

        if ($booking->key_status == Status::ENABLE) {
            $notify[] = ['error', 'Keys have already been given to the guest'];
            return back()->withNotify($notify);
        }

        if (now()->format('Y-m-d') < $booking->check_in) {
            $notify[] = ['error', 'You can\'t handover keys before the check-in date'];
            return back()->withNotify($notify);
        }

        if (now()->format('Y-m-d') >= $booking->check_out) {
            $notify[] = ['error', 'You can\'t handover keys after the check-out date'];
            return back()->withNotify($notify);
        }

        $booking->key_status = Status::ENABLE;
        $booking->checked_in_at = now();
        $booking->save();

        $booking->createActionHistory('key_handover');

        $notify[] = ['success', 'Key handover successfully'];
        return back()->withNotify($notify);
    }

    public function mergeBooking(Request $request, $id)
    {
        $parentBooking = Booking::currentOwner()->findOrFail($id);
        $request->merge(['merge_with' => $parentBooking->booking_number]);

        $request->validate([
            'booking_numbers'             => 'required|array',
            'booking_numbers.*'           => 'distinct|exists:bookings,booking_number|different:merge_with',
        ], [
            'booking_numbers.*.distinct' => 'Booking numbers should not be duplicate',
            'booking_numbers.*.different' => 'Booking numbers must be different from the booking number of merging with',
        ]);

        // Check if available to merge
        $check = Booking::currentOwner()->whereIn('booking_number', $request->booking_numbers)->where('status', '!=', 1)->first();

        if ($check) {
            $notify[] = ['error', $check->booking_number . ' can\'t be merged. Only active bookings are able to merge.'];
            return back()->withNotify($notify);
        }

        foreach ($request->booking_numbers as $bookingNumber) {
            $booking = Booking::currentOwner()->where('booking_number', $bookingNumber)->first();
            $booking->usedExtraService()->update(['booking_id' => $parentBooking->id]);
            $booking->bookedRooms()->update(['booking_id' => $parentBooking->id]);
            BookingActionHistory::where('booking_id', $booking->id)->delete();
            PaymentLog::where('booking_id', $booking->id)->update(['booking_id' => $parentBooking->id]);
            $keyStatus = $parentBooking->key_status == Status::KEY_GIVEN || $booking->key_status == Status::KEY_GIVEN ? 1 : 0;
            $parentBooking->total_discount   += $booking->total_discount;
            $parentBooking->tax_charge       += $booking->tax_charge;
            $parentBooking->booking_fare     += $booking->booking_fare;
            $parentBooking->service_cost     += $booking->service_cost;
            $parentBooking->extra_charge     += $booking->extra_charge;
            $parentBooking->extra_charge_subtracted  += $booking->extra_charge_subtracted;
            $parentBooking->paid_amount      += $booking->paid_amount;
            $parentBooking->cancellation_fee += $booking->cancellation_fee;
            $parentBooking->key_status        = $keyStatus;
            $parentBooking->save();
            $booking->delete();
        }

        $lastDateBookedRoom  = $parentBooking->activeBookedRooms()->orderBy('booked_for', 'desc')->first();
        $firstDateBookedRoom = $parentBooking->activeBookedRooms()->orderBy('booked_for', 'asc')->first();

        if ($lastDateBookedRoom) {
            $parentBooking->check_out = Carbon::parse($lastDateBookedRoom->booked_for)->addDay()->format('Y-m-d');
        }

        if ($firstDateBookedRoom) {
            $parentBooking->check_in = $firstDateBookedRoom->booked_for;
        }

        $parentBooking->save();

        $detail = implode(', ', $request->booking_numbers) . ' merged with ' . $parentBooking->booking_number;

        $parentBooking->createActionHistory('merged_booking', $detail);
        $notify[] = ['success', 'Bookings merged successfully'];
        return back()->withNotify($notify);
    }

    public function paymentView($id)
    {
        $booking           = Booking::currentOwner()->with('bookedRooms', 'payments', 'usedExtraService', 'user', 'guest')->findOrFail($id);
        $totalFare         = $booking->bookedRooms->sum('fare');
        $totalTaxCharge    = $booking->bookedRooms->sum('tax_charge');
        $canceledFare      = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('fare');
        $canceledTaxCharge = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('tax_charge');
        $returnedPayments  = $booking->payments->where('type', 'BOOKING_PAYMENT_RETURNED');
        $receivedPayments  = $booking->payments->where('type', 'BOOKING_PAYMENT_RECEIVED');
        $pageTitle         = "Bill Payment";
        return view('owner.booking.payment', compact('pageTitle', 'booking', 'totalFare', 'totalTaxCharge', 'canceledFare', 'canceledTaxCharge', 'returnedPayments', 'receivedPayments'));
    }
// Custom Note (KokanStays):
// This method handles both receiving payment and refunding guest.
// If due_amount > 0 → guest pays vendor
// If due_amount < 0 → vendor refunds guest (recorded as BOOKING_PAYMENT_RETURNED)
    public function payment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0'
        ]);

        $booking = Booking::currentOwner()->findOrFail($id);
        if ($booking->status == Status::BOOKING_CANCELED) {
        $notify[] = ['error', 'Cancelled bookings cannot be modified'];
        return back()->withNotify($notify);
        }

// real calculation (do NOT use accessor)
$due = $booking->total_amount - $booking->paid_amount;

if ($request->amount > abs($due)) {
    $message = $due < 0 
        ? 'Amount can\'t be greater than refundable amount' 
        : 'Amount can\'t be greater than receivable amount';

    $notify[] = ['error', $message];
    return back()->withNotify($notify);
}

if ($due > 0) {
    return $this->receivePayment($booking, $request->amount);
}

return $this->returnPayment($booking, $request->amount);
    }

    public function addExtraCharge(Request $request, $id)
    {
        $this->extraChargeValidation($request);

        $booking = Booking::currentOwner()->findOrFail($id);
        if ($booking->status == Status::BOOKING_CANCELED) {
        $notify[] = ['error', 'Cancelled bookings cannot be modified'];
        return back()->withNotify($notify);
        }
        $booking->extra_charge += $request->amount;
        $booking->save();
        $reason = showAmount($request->amount) . ' added for ' . $request->reason;

        $booking->createActionHistory('extra_charge_added', $reason);

        $notify[] = ['success', 'Extra charge added successfully'];
        return back()->withNotify($notify);
    }

    public function subtractExtraCharge(Request $request, $id)
    {
        $this->extraChargeValidation($request);

        $booking = Booking::currentOwner()->findOrFail($id);
        if ($booking->status == Status::BOOKING_CANCELED) {
        $notify[] = ['error', 'Cancelled bookings cannot be modified'];
        return back()->withNotify($notify);
        }

        if ($request->amount + $booking->extra_charge_subtracted > $booking->extra_charge) {
            $notify[] = ['error', 'Subtracted amount should be less than or equal to booking extra charge'];
            return back()->withNotify($notify);
        }

        $booking->extra_charge_subtracted += $request->amount;
        $booking->save();

        $reason = showAmount($request->amount) . ' subtracted for ' . $request->reason;

        $booking->createActionHistory('extra_charge_subtracted', $reason);

        $notify[] = ['success', 'Extra charge subtracted successfully'];
        return back()->withNotify($notify);
    }

    private function extraChargeValidation($request)
    {
        $request->validate([
            'amount' => 'required|numeric|gte:0',
            'reason' => 'required|string|max:255',
        ]);
    }

    public function checkOutPreview($id)
    {
        $booking           = Booking::currentOwner()->active()->with('bookedRooms', 'payments', 'usedExtraService', 'user', 'guest')->findOrFail($id);
        $totalFare         = $booking->bookedRooms->sum('fare');
        $totalTaxCharge    = $booking->bookedRooms->sum('tax_charge');
        $canceledFare      = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('fare');
        $canceledTaxCharge = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('tax_charge');
        $returnedPayments  = $booking->payments->where('type', 'BOOKING_PAYMENT_RETURNED');
        $receivedPayments  = $booking->payments->where('type', 'BOOKING_PAYMENT_RECEIVED');
        $pageTitle = "Check Out Booking";
        return view('owner.booking.check_out', compact('pageTitle', 'booking', 'totalFare', 'totalTaxCharge', 'canceledFare', 'canceledTaxCharge', 'returnedPayments', 'receivedPayments'));
    }

    public function checkOut($id)
    {
        $booking = Booking::currentOwner()->active()->with('payments')->withSum('usedExtraService', 'total_amount')->findOrFail($id);

        if ($booking->check_out > now()->toDateString()) {
            $notify[] = ['error', 'Checkout date for this booking is greater than now'];
            return back()->withNotify($notify);
        }

        $due = getAmount($booking->total_amount - $booking->paid_amount);

        if ($due > 0) {
            $notify[] = ['error', 'The guest should pay the payable amount first'];
            return back()->withNotify($notify);
        }

        if ($due < 0) {
            $notify[] = ['error', 'Refund the refundable amount to the guest first'];
            return back()->withNotify($notify);
        }

        $booking->createActionHistory('checked_out');

        $booking->activeBookedRooms()->update(['status' => Status::BOOKING_CHECKOUT]);
        $booking->status = Status::BOOKING_CHECKOUT;
        $booking->checked_out_at = now();

        $booking->save();

        $notify[] = ['success', 'Booking checked out successfully'];
        return redirect()->route('owner.booking.all')->withNotify($notify);
    }

    public function extraServiceDetail($id)
    {
        $booking = Booking::currentOwner()->where('id', $id)->firstOrFail();
        $services = UsedExtraService::where('booking_id', $id)->with('extraService', 'room', 'actionBy')->paginate(getPaginate());
        $pageTitle = 'Service Details - ' . $booking->booking_number;
        return view('owner.booking.service_details', compact('pageTitle', 'services'));
    }

    public function generateInvoice($bookingId)
    {
        $booking = Booking::currentOwner()->with([
            'activeBookedRooms' => function ($query) {
                $query->select('id','booking_id', 'room_id', 'fare', 'status', 'booked_for');
            },
            'activeBookedRooms.room:id,room_type_id,room_number',
            'activeBookedRooms.room.roomType:id,name',
            'usedExtraService.room',
            'usedExtraService.extraService',
            'user:id,firstname,lastname,username,email,mobile',
            'guest'
        ])->findOrFail($bookingId);

        $data = ['booking' => $booking];

        $pdf = PDF::loadView('partials.invoice', $data);

        return $pdf->stream($booking->booking_number . '.pdf');
    }

    protected function receivePayment($booking, $receivingAmount)
    {
        $this->deposit($booking, $receivingAmount);
        $booking->createPaymentLog($receivingAmount, 'BOOKING_PAYMENT_RECEIVED');
        $booking->createActionHistory('payment_received');
        $booking->paid_amount += $receivingAmount;
        $booking->save();

        $notify[] = ['success', 'Payment received successfully'];
        return back()->withNotify($notify);
    }

    protected function returnPayment($booking, $receivingAmount)
    {
        $booking->createPaymentLog($receivingAmount, 'BOOKING_PAYMENT_RETURNED');
        $booking->createActionHistory('payment_returned');

        $booking->paid_amount -= $receivingAmount;
        $booking->save();

        $notify[] = ['success', 'Payment completed successfully'];
        return back()->withNotify($notify);
    }

    protected function deposit($booking, $payableAmount)
    {
        $data = new Deposit();
        $data->user_id = $booking->user_id;
        $data->booking_id = $booking->id;
        $data->owner_id = authOwner()->id;
        $data->amount = $payableAmount;
        $data->charge = 0;
        $data->final_amount = $payableAmount;
        $data->btc_amount = 0;
        $data->trx = getTrx();
        $data->btc_wallet = "";
        $data->payment_try = 0;
        $data->status = Status::PAYMENT_SUCCESS;
        $data->save();
    }
}
