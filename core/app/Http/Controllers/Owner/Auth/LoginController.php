<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\OwnerLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laramin\Utility\Onumoti;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'vendor';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $pageTitle = "Owner Login";
        return view('owner.auth.login', compact('pageTitle'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('owner');
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }


        Onumoti::getData();

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    public function logout(Request $request)
    {
        $this->guard('owner')->logout();
        $request->session()->invalidate();
        return $this->loggedOut($request) ?: redirect($this->redirectTo);
    }


    public function authenticated(Request $request, $owner)
    {
        if ($owner->status == Status::USER_BAN) {
            $this->guard()->logout();
            $notify[] = ['error', 'Your account deactivated.'];
            return to_route('user.login')->withNotify($notify);
        }

        $owner->tv = $owner->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $owner->save();

        $ip = getRealIP();
        $exist = OwnerLogin::where('owner_ip', $ip)->first();
        $ownerLogin = new OwnerLogin();
        if ($exist) {
            $ownerLogin->longitude    = $exist->longitude;
            $ownerLogin->latitude     = $exist->latitude;
            $ownerLogin->city         = $exist->city;
            $ownerLogin->country_code = $exist->country_code;
            $ownerLogin->country      = $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $ownerLogin->longitude    = @implode(',', $info['long']);
            $ownerLogin->latitude     = @implode(',', $info['lat']);
            $ownerLogin->city         = @implode(',', $info['city']);
            $ownerLogin->country_code = @implode(',', $info['code']);
            $ownerLogin->country      = @implode(',', $info['country']);
        }

        $ownerAgent          = osBrowser();
        $ownerLogin->owner_id = $owner->id;
        $ownerLogin->owner_ip = $ip;

        $ownerLogin->browser = @$ownerAgent['browser'];
        $ownerLogin->os      = @$ownerAgent['os_platform'];
        $ownerLogin->save();

        return to_route('owner.dashboard');
    }
}
