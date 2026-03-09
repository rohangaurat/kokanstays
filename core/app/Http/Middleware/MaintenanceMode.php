<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;

class MaintenanceMode
{
    public function handle($request, Closure $next)
    {
        if (gs('maintenance_mode') == Status::ENABLE) {

            if ($request->is('api/*')) {

                // ================================
                // KOKANSTAYS CUSTOM FIX
                // Allow some APIs to work even in maintenance mode
                // These APIs are required by the Flutter app during startup
                // ================================
                if (
                    $request->is('api/sections/maintenance') ||
                    $request->is('api/language/*') ||
                    $request->is('api/general-setting') ||
                    $request->is('api/policies')
                ) {
                    return $next($request);
                }

                // ================================
                // KOKANSTAYS CUSTOM FIX
                // Return safe JSON structure so Flutter app does not crash
                // ================================
                $notify = ['Our application is currently in maintenance mode'];

                return response()->json([
                    'remark'  => 'maintenance_mode',
                    'status'  => 'error',
                    'message' => ['error' => $notify],
                    'data' => [
    'maintenance' => true,
    'general_setting' => null
]
                ]);
            } else {
                return to_route('maintenance');
            }
        }

        return $next($request);
    }
}