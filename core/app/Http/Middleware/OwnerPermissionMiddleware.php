<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class OwnerPermissionMiddleware
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
        $owner = authOwner();

        if ($owner->id != 1 && $owner->status == 0) {
            auth('owner')->logout();
            $notify[] = ['error', 'Your account has been blocked'];
            return back()->withNotify($notify);
        }

        if (!Role::hasPermission()) {
            return redirect()->route('owner.profile');
        }
        return $next($request);
    }
}
