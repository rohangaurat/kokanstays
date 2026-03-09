<?php

namespace App\Http\Controllers\Owner;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;


class AuthorizationController extends Controller
{
    protected function checkCodeValidity($owner, $addMin = 2)
    {
        if (!$owner->ver_code_send_at) {
            return false;
        }
        if ($owner->ver_code_send_at->addMinutes($addMin) < Carbon::now()) {
            return false;
        }
        return true;
    }

    public function authorizeForm()
    {
        $owner = authOwner();
        if ($owner->status == 0) {
            $pageTitle = 'Banned';
            $type = 'ban';
        } elseif ($owner->status == 2) {
            auth()->guard('owner')->logout();
            $notify[] = ['error', 'Your account is currently under review.'];
            return to_route('owner.login')->withNotify($notify);
        } elseif (!$owner->tv) {
            $pageTitle = '2FA Verification';
            $type = '2fa';
        } else {
            return to_route('owner.dashboard');
        }

        return view('owner.auth.authorization.' . $type, compact('owner', 'pageTitle'));
    }

    public function sendVerifyCode($type)
    {
        $owner = authOwner();

        if ($this->checkCodeValidity($owner)) {
            $targetTime = $owner->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $targetTime - time();
            throw ValidationException::withMessages(['resend' => 'Please try after ' . $delay . ' seconds']);
        }

        $owner->ver_code = verificationCode(6);
        $owner->ver_code_send_at = Carbon::now();
        $owner->save();

        if ($type == 'email') {
            $type = 'email';
            $notifyTemplate = 'EVER_CODE';
        } else {
            $type = 'sms';
            $notifyTemplate = 'SVER_CODE';
        }

        notify($owner, $notifyTemplate, [
            'code' => $owner->ver_code
        ], [$type]);

        $notify[] = ['success', 'Verification code sent successfully'];
        return back()->withNotify($notify);
    }

    public function g2faVerification(Request $request)
    {
        $owner = authOwner();
        $request->validate([
            'code' => 'required',
        ]);
        $response = verifyG2fa($owner, $request->code);
        if ($response) {
            $notify[] = ['success', 'Verification successful'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }
}
