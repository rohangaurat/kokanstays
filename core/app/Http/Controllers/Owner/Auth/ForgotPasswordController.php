<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\OwnerPasswordReset;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->middleware('owner.guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm() {
        $pageTitle = 'Account Recovery';
        return view('owner.auth.passwords.email', compact('pageTitle'));
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker() {
        return Password::broker('owners');
    }

    public function sendResetCodeEmail(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $owner = Owner::where('email', $request->email)->first();
        if (!$owner) {
            return back()->withErrors(['Email Not Available']);
        }

        if ($owner->status == 2) {
            $notify[] = ['error', 'Your account is currently under review.'];
            return to_route('owner.login')->withNotify($notify);
        }

        $code                           = verificationCode(6);
        $ownerPasswordReset             = new OwnerPasswordReset();
        $ownerPasswordReset->email      = $owner->email;
        $ownerPasswordReset->token      = $code;
        $ownerPasswordReset->created_at = date("Y-m-d h:i:s");
        $ownerPasswordReset->save();

        $ownerBrowser = osBrowser();
        notify($owner, 'PASS_RESET_CODE', [
            'code'             => $code,
            'operating_system' => isset($ownerBrowser['os_platform']) ? $ownerBrowser['os_platform'] : '',
            'browser'          => isset($ownerBrowser['browser']) ? $ownerBrowser['browser'] : '',
            'ip'               => getRealIp(),
            'time'             => date('Y-m-d h:i:s A'),
        ], ['email'], false);

        $email = $owner->email;
        session()->put('pass_res_mail', $email);

        return to_route('owner.password.code.verify');
    }

    public function codeVerify() {
        $pageTitle = 'Verify Code';
        $email     = session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error', 'Oops! session expired'];
            return to_route('owner.password.reset')->withNotify($notify);
        }
        return view('owner.auth.passwords.code_verify', compact('pageTitle', 'email'));
    }

    public function verifyCode(Request $request) {
        $request->validate(['code' => 'required']);
        $notify[] = ['success', 'You can change your password.'];
        $code     = str_replace(' ', '', $request->code);
        return to_route('owner.password.reset.form', $code)->withNotify($notify);
    }
}
