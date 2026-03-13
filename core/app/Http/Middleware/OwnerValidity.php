<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class OwnerValidity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
public function handle(Request $request, Closure $next)
{
    $owner = getParentOwner();

    $paymentDeadLine = null;

    if ($owner->expire_at != null) {
        $paymentBefore = (int) gs('payment_before'); // convert string to int
        $paymentDeadLine = Carbon::parse($owner->expire_at)->addDays($paymentBefore);
    }

    if ($paymentDeadLine && now() >= $paymentDeadLine) {
        if (!authOwner()->parent_id) {

            $message = authOwner()->deposits()->where('pay_for_month', '>', 0)->count() > 0
                ? 'Your payment deadline has been exceed, you have to pay first.'
                : 'You have to pay monthly bill first.';

            $notify[] = ['error', $message];
            return to_route('owner.dashboard')->withNotify($notify);
        }

        $notify[] = ['error', 'Unauthorized action'];
        return to_route('owner.profile')->withNotify($notify);
    }

    return $next($request);
    }
}
