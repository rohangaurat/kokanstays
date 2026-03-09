<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function methods($bookingId)
    {
        $booking = Booking::active()->where('user_id', auth()->id())->find($bookingId);

        if (!$booking) {
            $notify[] = 'Booking not found';
            return responseError('validation_error', $notify);
        }

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        $notify[] = 'Payment Methods';

        return responseSuccess('payment_methods', $notify, [
            'booking' => $booking,
            'methods' => $gatewayCurrency,
            'image_path' => getFilePath('gateway')
        ]);
    }

    public function paymentInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
            'booking_id'  => 'required'
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();
        $booking = Booking::active()->where('user_id', $user->id)->where('id', $request->booking_id)->first();

        if (!$booking) {
            $notify[] = 'Booking not found';
            return responseError('validation_error', $notify);
        }

        if ($request->amount > $booking->due_amount) {
            $notify[] = 'Amount should be less than or equal to due amount';
            return responseError('validation_error', $notify);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = 'Invalid gateway';
            return responseError('validation_error', $notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] =  'Please follow payment limit';
            return responseError('validation_error', $notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data = new Deposit();
        $data->from_api = 1;
        $data->is_web = $request->is_web ? 1 : 0;
        $data->user_id = $user->id;
        $data->owner_id = $booking->owner_id;
        $data->booking_id = $booking->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->success_url = $request->success_url ?? route('home');
        $data->failed_url = $request->failed_url ?? route('home');
        $data->trx = getTrx();
        $data->save();

        $data->load('gateway', 'gateway.form');
        $notify[] =  'Payment initiated';

        return responseSuccess('payment_initiated', $notify, [
            'deposit' => $data,
            'redirect_url' => route('deposit.app.confirm', encrypt($data->id))
        ]);
    }
}
