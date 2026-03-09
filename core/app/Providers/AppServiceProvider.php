<?php

namespace App\Providers;

use App\Constants\Status;
use App\Lib\Searchable;
use App\Models\AdminNotification;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Owner;
use App\Models\OwnerNotification;
use App\Models\Role;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);

        Blade::if('can', function ($permission) {
            return Role::hasPermission($permission);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load language from cookie
        if (request()->hasCookie('site_language')) {
        app()->setLocale(request()->cookie('site_language'));
            }
        if (!cache()->get('SystemInstalled')) {
            $envFilePath = base_path('.env');
            if (!file_exists($envFilePath)) {
                header('Location: install');
                exit;
            }
            $envContents = file_get_contents($envFilePath);
            if (empty($envContents)) {
                header('Location: install');
                exit;
            } else {
                cache()->put('SystemInstalled', true);
            }
        }

        $viewShare['emptyMessage'] = 'Data not found';
        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                'pendingTicketCount'         => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                'pendingDepositsCount'    => Deposit::pending()->count(),
                'pendingWithdrawCount'    => Withdrawal::pending()->count(),
                'bannedOwnersCount'           => Owner::owner()->banned()->count(),
                'ownerRequestCount'           => Owner::ownerRequest()->count(),
                'updateAvailable'    => version_compare(gs('available_version'), systemDetails()['version'], '>') ? 'v' . gs('available_version') : false,
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications' => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        view()->composer('owner.partials.sidenav', function ($view) {
            $view->with([
                'bookingRequestCount' => BookingRequest::currentOwner()->initial()->count(),
                'delayedCheckoutCount' => Booking::currentOwner()->delayedCheckout()->count(),
                'refundableBookingCount' => Booking::currentOwner()->refundable()->count(),
                'pendingCheckInsCount' => Booking::currentOwner()->active()->keyNotGiven()->whereDate('check_in', '<=', now())->count()
            ]);
        });

        view()->composer('owner.partials.topnav', function ($view) {
            $view->with([
                'ownerNotifications' => OwnerNotification::currentOwner()->where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'ownerNotificationCount' => OwnerNotification::currentOwner()->where('is_read', Status::NO)->count(),
            ]);
        });

        if (gs('force_ssl')) {
            \URL::forceScheme('https');
        }


        Paginator::useBootstrapFive();
    }
}
