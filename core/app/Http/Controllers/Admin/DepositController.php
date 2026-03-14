<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Booking;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function pending($userId = null)
    {
        $pageTitle = 'Pending Deposits';
        $deposits = $this->depositData('pending', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }


    public function approved($userId = null)
    {
        $pageTitle = 'Approved Deposits';
        $deposits = $this->depositData('approved', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function successful($userId = null)
    {
        $pageTitle = 'Successful Deposits';
        $deposits = $this->depositData('successful', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function rejected($userId = null)
    {
        $pageTitle = 'Rejected Deposits';
        $deposits = $this->depositData('rejected', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function initiated($userId = null)
    {
        $pageTitle = 'Initiated Deposits';
        $deposits = $this->depositData('initiated', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function deposit($userId = null)
    {
        $pageTitle = 'Deposit History';
        $depositData = $this->depositData($scope = null, $summary = true, userId: $userId);
        $deposits = $depositData['data'];
        $summary = $depositData['summary'];
        $successful = $summary['successful'];
        $pending = $summary['pending'];
        $rejected = $summary['rejected'];
        $initiated = $summary['initiated'];
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'successful', 'pending', 'rejected', 'initiated'));
    }

    protected function depositData($scope = null, $summary = false, $userId = null)
    {
        if ($scope) {
            $deposits = Deposit::$scope()->with(['user', 'gateway', 'owner']);
        } else {
            $deposits = Deposit::with(['user', 'gateway', 'owner']);
        }

        if ($userId) {
            $deposits = $deposits->where('user_id', $userId);
        }

        $deposits = $deposits->searchable(['trx', 'user:username', 'owner:email'])->dateFilter();

        $request = request();

        if ($request->method) {
            if ($request->method != Status::GOOGLE_PAY) {
                $method = Gateway::where('alias', $request->method)->firstOrFail();
                $deposits = $deposits->where('method_code', $method->code);
            } else {
                $deposits = $deposits->where('method_code', Status::GOOGLE_PAY);
            }
        }

        if ($request->payment_by && ($request->payment_by == 'user_id' || $request->payment_by == 'owner_id')) {
            $paymentBy = $request->payment_by;
            $deposits = $deposits->where($paymentBy, '!=', 0);
        }

        if (!$summary) {
            return $deposits->orderBy('id', 'desc')->paginate(getPaginate());
        } else {
            $successful = clone $deposits;
            $pending = clone $deposits;
            $rejected = clone $deposits;
            $initiated = clone $deposits;

            $successfulSummary = $successful->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
            $pendingSummary = $pending->where('status', Status::PAYMENT_PENDING)->sum('amount');
            $rejectedSummary = $rejected->where('status', Status::PAYMENT_REJECT)->sum('amount');
            $initiatedSummary = $initiated->where('status', Status::PAYMENT_INITIATE)->sum('amount');

            return [
                'data' => $deposits->orderBy('id', 'desc')->paginate(getPaginate()),
                'summary' => [
                    'successful' => $successfulSummary,
                    'pending' => $pendingSummary,
                    'rejected' => $rejectedSummary,
                    'initiated' => $initiatedSummary,
                ]
            ];
        }
    }

    public function details($id)
    {
        $deposit = Deposit::where('id', $id)->with(['user', 'owner', 'gateway'])->firstOrFail();

        if ($deposit->user_id) {
            $pageTitle = $deposit->user->username . ' requested ' . showAmount($deposit->amount);
        } elseif ($deposit->owner_id) {
            $pageTitle = $deposit->owner->fullname . ' requested ' . showAmount($deposit->amount);
        }

        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('pageTitle', 'deposit', 'details'));
    }


    public function approve($id)
{
    $deposit = Deposit::where('id', $id)->where('status', Status::PAYMENT_PENDING)->firstOrFail();

    if ($deposit->booking_id) {
        $booking = Booking::find($deposit->booking_id);
        bookingActionRecord($deposit->booking_id, $booking->owner_id, 'payment_approved');
    }

    PaymentController::userDataUpdate($deposit, true);

    // Vendor subscription notification
    if ($deposit->owner_id != 0 && $deposit->pay_for_month != 0) {
        $owner = $deposit->owner;

        notify($owner, 'BILL_PAYMENT_APPROVED', [
            'total_month'      => $deposit->pay_for_month,
            'amount'           => showAmount($deposit->amount, currencyFormat: false),
            'charge'           => showAmount($deposit->charge, currencyFormat: false),
            'rate'             => $deposit->rate,
            'method_name'      => $deposit->gateway->name,
            'method_currency'  => $deposit->method_currency,
            'method_amount'    => showAmount($deposit->final_amount, currencyFormat: false),
        ]);
    }

    $notify[] = ['success', 'Payment request approved successfully'];

    return to_route('admin.deposit.pending')->withNotify($notify);
}

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|string|max:255'
        ]);
        $deposit = Deposit::where('id', $request->id)->where('status', Status::PAYMENT_PENDING)->firstOrFail();

        $deposit->admin_feedback = $request->message;
        $deposit->status = Status::PAYMENT_REJECT;
        $deposit->save();

        if ($deposit->owner_id != 0 && $deposit->pay_for_month != 0) {
    $owner = $deposit->owner;

    notify($owner, 'BILL_PAYMENT_MANUAL_REJECT', [
        'total_month'      => $deposit->pay_for_month,
        'amount'           => showAmount($deposit->amount, currencyFormat: false),
        'charge'           => showAmount($deposit->charge, currencyFormat: false),
        'rate'             => $deposit->rate,
        'method_name'      => $deposit->gateway->name,
        'method_currency'  => $deposit->method_currency,
        'method_amount'    => showAmount($deposit->final_amount, currencyFormat: false),
        'rejection_reason' => $deposit->admin_feedback
    ]);

    // Vendor dashboard notification 🔔
    $ownerNotification = new \App\Models\OwnerNotification();
    $ownerNotification->owner_id  = $owner->id;
    $ownerNotification->user_id   = 0;
    $ownerNotification->title     = 'Subscription payment rejected';
    $ownerNotification->click_url = urlPath('owner.payment.history') . '?search=' . $deposit->trx;
    $ownerNotification->save();
} elseif ($deposit->booking_id) {
            $user = $deposit->user;
            $booking = Booking::find($deposit->booking_id);

            //action log
            bookingActionRecord($deposit->booking_id, $booking->owner_id, 'payment_reject');

            notify($user, 'PAYMENT_MANUAL_REJECT', [
                'booking_number'  => $booking->booking_number,
                'amount'          => showAmount($deposit->amount, currencyFormat: false),
                'charge'          => showAmount($deposit->charge, currencyFormat: false),
                'rate'            => $deposit->rate,
                'method_name'     => $deposit->gateway->name,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false)
            ]);
        }

        $notify[] = ['success', 'Payment request rejected successfully'];
        return  to_route('admin.deposit.pending')->withNotify($notify);
    }
}
