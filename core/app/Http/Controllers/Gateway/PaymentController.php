<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Booking;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Owner;
use App\Models\OwnerNotification;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function deposit($id = null) {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        if (!$id && auth('owner')->check()) {
            $pageTitle      = 'Subscription';
            $pendingPayment = Deposit::pending()->where('owner_id', getOwnerParentId())->latest()->first();
            return view('owner.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'pendingPayment'));
        } else if ($id && auth()->check()) {
            $pageTitle = 'Deposit Methods';
            $user      = auth()->user();
            $booking   = Booking::where('user_id', $user->id)->find($id);
            if (!$booking) {
                $notify[] = ['error', 'Booking not found'];
                return back()->withNotify($notify);
            }
            if ($booking->due_amount == 0) {
                $notify[] = ['error', 'No due amount for this booking'];
                return back()->withNotify($notify);
            }
            return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'booking'));
        } else {
            abort(404);
        }
    }

    public function depositInsert(Request $request) {
        $currentAuth      = currentAuth();
        $payFor           = 'nullable';
        $amountIsRequired = 'required';
        if ($currentAuth['type'] == 'owner') {
            $payFor           = 'required|integer|gt:0|lte:' . gs('maximum_payment_month');
            $amountIsRequired = 'nullable';
        }

        $request->validate([
            'amount'        => $amountIsRequired . '|numeric|gt:0',
            'gateway'       => 'required',
            'currency'      => 'required',
            'pay_for_month' => $payFor,
            'booking_id'    => $amountIsRequired . '|exists:bookings,id',
        ]);

        $owner   = null;
        $booking = null;
        $amount  = $request->amount;
        $ownerId = 0;

        if ($currentAuth['type'] == 'owner') {
            $owner   = authOwner();
            $ownerId = $owner->id;
            $amount  = $request->pay_for_month * gs('bill_per_month');

            if ($request->gateway == -1) {
                if ($amount > $owner->balance) {
                    $notify[] = ['error', 'Insufficient balance'];
                    return back()->withNotify($notify);
                }

                $this->billPayByWalletBalance($request, $amount);

                $notify[] = ['success', 'Payment completed successfully'];
                return to_route('owner.dashboard')->withNotify($notify);
            }
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        //for user booking  only
        if ($currentAuth['type'] == 'user') {
            $bookingId = session()->get('booking_id') ?? $request->booking_id;
            $booking   = Booking::find($bookingId);
            $ownerId   = $booking->owner_id;

            if ($amount > ($booking->total_amount - $booking->paid_amount)) {
                $notify[] = ['error', 'Amount should be less than or equal to payable amount'];
                return back()->withNotify($notify);
            }
        }
        //end

        $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable     = $amount + $charge;
        $finalAmount = $payable * $gate->rate;

        if ($currentAuth['type'] == 'owner') {
            $redirectTo = 'owner.deposit.confirm';
            $successURL = 'owner.payment.history';
            $failedURL  = 'owner.deposit.index';
        } else {
            $redirectTo = 'user.deposit.confirm';
            $failedURL  = 'user.deposit.index';
            $successURL = 'user.booking.history';
        }

        $data                  = new Deposit();
        $data->user_id         = auth()->user() ? auth()->user()->id : 0;
        $data->owner_id        = $ownerId;
        $data->pay_for_month   = $request->pay_for_month ?? 0;
        $data->booking_id      = $booking->id ?? 0;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->success_url     = route($successURL);
        $data->failed_url      = route($failedURL);
        $data->save();

        session()->put('Track', $data->trx);
        return to_route($redirectTo);
    }

    private function billPayByWalletBalance($request, $amount)
{
    $owner       = authOwner();
    $payForMonth = intval($request->pay_for_month);
    $trx         = getTrx();

    $nextExpireDate = Carbon::parse($owner->expire_at)
        ->addMonth($payForMonth)
        ->subDay();

    $owner->balance -= $amount;
    $owner->expire_at = $nextExpireDate;
    $owner->save();

    // Create Deposit record (for Subscription History)
    $deposit = new Deposit();
    $deposit->owner_id = $owner->id;
    $deposit->user_id = 0;
    $deposit->pay_for_month = $payForMonth;
    $deposit->method_code = -1; // Wallet
    $deposit->method_currency = gs('cur_text');
    $deposit->amount = $amount;
    $deposit->charge = 0;
    $deposit->rate = 1;
    $deposit->final_amount = $amount;
    $deposit->trx = $trx;
    $deposit->status = Status::PAYMENT_SUCCESS;
    $deposit->save();

    // Transaction record
    $transaction = new Transaction();
    $transaction->owner_id = $owner->id;
    $transaction->amount = $amount;
    $transaction->post_balance = $owner->balance;
    $transaction->charge = 0;
    $transaction->trx_type = '-';
    $transaction->details = 'Wallet payment for ' . $payForMonth . ' months subscription';
    $transaction->trx = $trx;
    $transaction->remark = 'monthly_bill_payment';
    $transaction->save();

    notify($owner, 'BILL_PAYMENT_COMPLETED', [
        'amount_per_month' => showAmount($transaction->amount / $payForMonth, currencyFormat: false),
        'total_month'      => $payForMonth,
        'amount'           => showAmount($transaction->amount, currencyFormat: false),
        'charge'           => showAmount($transaction->charge, currencyFormat: false),
        'final_amount'     => showAmount($transaction->amount, currencyFormat: false),
        'expire_at'        => showDateTime($owner->expire_at, 'd M, Y'),
        'trx'              => $transaction->trx,
    ]);
}

    public function appDepositConfirm($hash) {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function depositConfirm() {
        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            if ($deposit->user_id) {
                return to_route('user.deposit.manual.confirm');
            } else {
                return to_route('owner.deposit.manual.confirm');
            }
        }

        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        if (currentAuth()['type'] == 'owner') {
            $view = $data->view;
            $view = str_replace('user.', 'owner.', $view);
        } else {
            $view = "Template::" . $data->view;
        }

        $pageTitle = 'Payment Confirm';
        return view($view, compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null) {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $methodName = $deposit->methodName();

            if ($deposit->pay_for_month != 0) {
                $owner = Owner::find($deposit->owner_id);
                $owner->balance += $deposit->amount;
                $owner->save();

                $transaction               = new Transaction();
                $transaction->owner_id     = $owner->id;
                $transaction->amount       = $deposit->amount;
                $transaction->post_balance = $owner->balance;
                $transaction->charge       = $deposit->charge;
                $transaction->trx_type     = '+';
                $transaction->details      = 'Payment via ' . $methodName;
                $transaction->trx          = $deposit->trx;
                $transaction->remark       = 'payment';
                $transaction->save();

                $nextExpireDate = Carbon::parse($owner->expire_at)->addMonth($deposit->pay_for_month)->subDay();

                $owner->expire_at = $nextExpireDate;
                $owner->balance -= $deposit->amount;
                $owner->save();

                $transaction               = new Transaction();
                $transaction->owner_id     = $owner->id;
                $transaction->amount       = $deposit->amount;
                $transaction->post_balance = $owner->balance;
                $transaction->charge       = $deposit->charge;
                $transaction->trx_type     = '-';
                $transaction->details      = 'Payment via ' . $methodName . ' for ' . $deposit->pay_for_month . ' months';
                $transaction->trx          = $deposit->trx;
                $transaction->remark       = 'monthly_bill_payment';
                $transaction->save();

                notify($owner, 'BILL_PAYMENT_COMPLETED', [
                    'amount_per_month' => showAmount($deposit->amount / $deposit->pay_for_month, currencyFormat: false),
                    'total_month'      => $deposit->pay_for_month,
                    'amount'           => showAmount($deposit->amount, currencyFormat: false),
                    'charge'           => showAmount($transaction->charge, currencyFormat: false),
                    'final_amount'     => showAmount($deposit->final_amount, currencyFormat: false),
                    'expire_at'        => showDateTime($owner->expire_at, 'd M, Y'),
                    'trx'              => $transaction->trx,
                ]);
            } else {
                $user = User::find($deposit->user_id);
                //update booking
                $booking = Booking::find($deposit->booking_id);
                $booking->paid_amount += $deposit->amount;
                $booking->save();

                //payment log
                $booking->createPaymentLog($deposit->amount, 'BOOKING_PAYMENT_RECEIVED', @$deposit->gateway->name, true, $booking->owner_id);

                $owner = Owner::find($booking->owner_id);
                $owner->balance += $deposit->amount;
                $owner->save();

                $transaction               = new Transaction();
                $transaction->owner_id     = $owner->id;
                $transaction->user_id      = $user->id;
                $transaction->amount       = $deposit->amount;
                $transaction->post_balance = $owner->balance;
                $transaction->charge       = $deposit->charge;
                $transaction->trx_type     = '+';
                $transaction->details      = 'Payment for booking via ' . $methodName;
                $transaction->trx          = $deposit->trx;
                $transaction->remark       = 'booking_payment';
                $transaction->save();

                $ownerNotification            = new OwnerNotification();
                $ownerNotification->owner_id  = $owner->id;
                $ownerNotification->user_id   = $user->id;
                $ownerNotification->title     = 'Payment for booking via ' . $methodName;
                $ownerNotification->click_url = urlPath('owner.report.payments.received') . '?search=' . $booking->booking_number;
                $ownerNotification->save();

                $ownerNotification            = new AdminNotification();
                $ownerNotification->user_id   = $user->id;
                $ownerNotification->title     = 'Payment for booking via ' . $methodName;
                $ownerNotification->click_url = urlPath('admin.deposit.successful') . '?search=' . $deposit->trx;
                $ownerNotification->save();

                notify($user, $isManual ? 'PAYMENT_MANUAL_APPROVED' : 'DIRECT_PAYMENT_SUCCESSFUL', [
                    'booking_number'  => $booking->booking_number,
                    'amount'          => showAmount($deposit->amount, currencyFormat: false),
                    'charge'          => showAmount($deposit->charge, currencyFormat: false),
                    'rate'            => showAmount($deposit->rate, currencyFormat: false),
                    'method_name'     => $methodName,
                    'method_currency' => $deposit->method_currency,
                    'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
                    'trx'             => $deposit->trx,
                ]);
            }
        }
    }

    public function manualDepositConfirm() {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Deposit';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;

            if ($data->user_id) {
                return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
            } else {
                return view('owner.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
            }
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $methodName = $data->methodName();

        if ($data->user_id) {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $data->user_id;
            $adminNotification->title     = 'Payment request from ' . $data->user->username;
            $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
            $adminNotification->save();

            $user    = user::find($data->user_id);
            $booking = Booking::find($data->booking_id);

            notify($user, 'PAYMENT_MANUAL_REQUEST', [
                'booking_number'  => $booking->booking_number,
                'amount'          => showAmount($data->amount, currencyFormat: false),
                'charge'          => showAmount($data->charge, currencyFormat: false),
                'rate'            => showAmount($data->rate, currencyFormat: false),
                'method_name'     => $methodName,
                'method_currency' => $data->method_currency,
                'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
                'trx'             => $data->trx,
            ]);

            $url = 'user.deposit.history';
        } else {
            $adminNotification            = new AdminNotification();
            $adminNotification->owner_id  = $data->owner_id;
            $adminNotification->title     = 'Monthly Bill payment request from ' . $data->owner->fullname;
            $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
            $adminNotification->save();

            $owner = $data->owner;

            notify($owner, 'BILL_PAYMENT_MANUAL', [
                'total_month'     => $data->pay_for_month,
                'amount'          => showAmount($data->amount, currencyFormat: false),
                'charge'          => showAmount($data->charge, currencyFormat: false),
                'rate'            => showAmount($data->rate, currencyFormat: false),
                'method_name'     => $methodName,
                'method_currency' => $data->method_currency,
                'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
                'trx'             => $data->trx,
            ]);

            $url = 'owner.payment.history';
        }

        $notify[] = ['success', 'Your payment request has been taken'];
        return to_route($url)->withNotify($notify);
    }
}
