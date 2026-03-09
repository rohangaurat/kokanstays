<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'owner')
    {
        if (!Auth::guard($guard)->check()) {
            return to_route('owner.login');
        }

        if (Auth::guard($guard)->user()->status == Status::USER_BAN) {
            Auth::guard($guard)->logout();
            $notify[] = ['error', 'This account is banned'];
            return to_route('owner.login')->withNotify($notify);
        }

        return $next($request);
    }
}
