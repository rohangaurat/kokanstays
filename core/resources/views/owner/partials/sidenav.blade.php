<div class="sidebar bg--dark"">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a class="sidebar__main-logo" href="{{ route('owner.dashboard') }}"><img alt="@lang('image')"
                    src="{{ siteLogo() }}"></a>
        </div>
        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                @can('owner.dashboard')
                    <li class="sidebar-menu-item {{ menuActive('owner.dashboard') }}">
                        <a class="nav-link" href="{{ route('owner.dashboard') }}">
                            <i class="menu-icon las la-home"></i>
                            <span class="menu-title">@lang('Dashboard')</span>
                        </a>
                    </li>
                @endcan
                @can(['owner.hotel.setting.index', 'owner.hotel.setting.payment.systems', 'owner.hotel.room.type.all',
                    'owner.hotel.room.all', 'owner.hotel.extra_services.all'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a class="{{ menuActive('owner.hotel*', 3) }}" href="javascript:void(0)">
                            <i class="menu-icon las la-city"></i>
                            <span class="menu-title">@lang('Manage Hotel')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('owner.hotel*', 2) }}">
                            <ul>
                                @can('owner.hotel.setting.index')
                                    <li class="sidebar-menu-item {{ menuActive('owner.hotel.setting.index') }}">
                                        <a class="nav-link" href="{{ route('owner.hotel.setting.index') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Settings')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('owner.hotel.setting.payment.systems')
                                    <li class="sidebar-menu-item {{ menuActive('owner.hotel.setting.payment.systems') }}">
                                        <a class="nav-link" href="{{ route('owner.hotel.setting.payment.systems') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Payment Systems')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('owner.hotel.room.type.all')
                                    <li class="sidebar-menu-item {{ menuActive('owner.hotel.room.type.*') }}">
                                        <a class="nav-link" href="{{ route('owner.hotel.room.type.all') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Room Types')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('owner.hotel.room.all')
                                    <li class="sidebar-menu-item {{ menuActive('owner.hotel.room.all') }}">
                                        <a class="nav-link" href="{{ route('owner.hotel.room.all') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Room')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('owner.hotel.extra_services.all')
                                    <li class="sidebar-menu-item {{ menuActive('owner.hotel.extra_services.*') }}">
                                        <a class="nav-link" href="{{ route('owner.hotel.extra_services.all') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Premium Services')</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan
                @can(['owner.staff.*', 'owner.roles.*'])
                    @can(['owner.staff.*', 'owner.roles.*'])
                        <li class="sidebar-menu-item sidebar-dropdown">
                            <a class="{{ menuActive(['owner.staff*', 'owner.roles.*'], 3) }}" href="javascript:void(0)">
                                <i class="menu-icon las la-users"></i>
                                <span class="menu-title">@lang('Manage Staff')</span>
                            </a>
                            <div class="sidebar-submenu {{ menuActive(['owner.staff*', 'owner.roles.*'], 2) }}">
                                <ul>
                                    @can('owner.staff.index')
                                        <li class="sidebar-menu-item {{ menuActive('owner.staff*') }}">
                                            <a class="nav-link" href="{{ route('owner.staff.index') }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Staff')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('owner.roles.index')
                                        <li class="sidebar-menu-item {{ menuActive('owner.roles*') }}">
                                            <a class="nav-link" href="{{ route('owner.roles.index') }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Roles')</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcan
                @endcan
                @can('owner.review.index')
                    <li class="sidebar-menu-item {{ menuActive('owner.review.*') }}">
                        <a class="nav-link" href="{{ route('owner.review.index') }}">
                            <i class="menu-icon lar la-star"></i>
                            <span class="menu-title">@lang('Reviews')</span>
                        </a>
                    </li>
                @endcan
                @can(['owner.request.booking.all', 'owner.booking.todays.booked', 'owner.booking.todays.checkin',
                    'owner.pending.booking.checkin', 'owner.booking.todays.checkout', 'owner.delayed.booking.checkout',
                    'owner.upcoming.booking.checkin', 'owner.upcoming.booking.checkout'])
                    <li class="sidebar__menu-header">@lang('Acknowledgement')</li>
                    @can('owner.request.booking.all')
                        <li class="sidebar-menu-item {{ menuActive('owner.request.booking.all') }}">
                            <a class="nav-link" href="{{ route('owner.request.booking.all') }}">
                                <i class="menu-icon la la-hand-point-right"></i>
                                <span class="menu-title">@lang('Booking Requests')</span>
                                @if ($bookingRequestCount)
                                    <span class="menu-badge pill bg--danger ms-auto">
                                        {{ $bookingRequestCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endcan
                    @can('owner.booking.todays.booked')
                        <li class="sidebar-menu-item {{ menuActive('owner.booking.todays.booked') }}">
                            <a class="nav-link" href="{{ route('owner.booking.todays.booked') }}">
                                <i class="menu-icon las la-calendar-day"></i>
                                <span class="menu-title">@lang('Todays Booked')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.booking.todays.checkin')
                        <li class="sidebar-menu-item {{ menuActive('owner.booking.todays.checkin') }}">
                            <a class="nav-link" href="{{ route('owner.booking.todays.checkin') }}">
                                <i class="menu-icon las la-door-open"></i>
                                <span class="menu-title">@lang('Todays Checkin')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.pending.booking.checkin')
                        <li class="sidebar-menu-item {{ menuActive('owner.pending.booking.checkin') }}">
                            <a class="nav-link" href="{{ route('owner.pending.booking.checkin') }}">
                                <i class="menu-icon la la-spinner"></i>
                                <span class="menu-title">@lang('Pending Check-Ins')</span>
                                @if ($pendingCheckInsCount)
                                    <span class="menu-badge pill bg--danger ms-auto">
                                        {{ $pendingCheckInsCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endcan
                    @can('owner.booking.todays.checkout')
                        <li class="sidebar-menu-item {{ menuActive('owner.booking.todays.checkout') }}">
                            <a class="nav-link" href="{{ route('owner.booking.todays.checkout') }}">
                                <i class="menu-icon las la-door-closed"></i>
                                <span class="menu-title">@lang('Todays Checkout')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.delayed.booking.checkout')
                        <li class="sidebar-menu-item {{ menuActive('owner.delayed.booking.checkout') }}">
                            <a class="nav-link" href="{{ route('owner.delayed.booking.checkout') }}">
                                <i class="menu-icon las la-calendar-day"></i>
                                <span class="menu-title">@lang('Delayed Checkouts')</span>
                                @if ($delayedCheckoutCount)
                                    <span class="menu-badge pill bg--danger ms-auto">
                                        {{ $delayedCheckoutCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endcan
                    @can('owner.upcoming.booking.checkin')
                        <li class="sidebar-menu-item {{ menuActive('owner.upcoming.booking.checkin') }}">
                            <a class="nav-link" href="{{ route('owner.upcoming.booking.checkin') }}">
                                <i class="menu-icon la la-sign-in"></i>
                                <span class="menu-title">@lang('Upcoming Check-Ins')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.upcoming.booking.checkout')
                        <li class="sidebar-menu-item {{ menuActive('owner.upcoming.booking.checkout') }}">
                            <a class="nav-link" href="{{ route('owner.upcoming.booking.checkout') }}">
                                <i class="menu-icon la la-sign-out transform-rotate-180"></i>
                                <span class="menu-title">@lang('Upcoming Checkouts')</span>
                            </a>
                        </li>
                    @endcan
                @endcan
                @php
                    $manageBookingMenus = [
                        'owner.booking.active',
                        'owner.booking.checked.out.list',
                        'owner.booking.canceled.list',
                        'owner.booking.refundable',
                        'owner.booking.checkout.delayed',
                        'owner.booking.all',
                    ];
                @endphp
                @can(array_merge($manageBookingMenus, ['owner.book.room', 'owner.extra.service.*']))
                    <li class="sidebar__menu-header">@lang('Booking')</li>
                    @can('owner.book.room')
                        <li class="sidebar-menu-item {{ menuActive('owner.book.room') }}">
                            <a class="nav-link" href="{{ route('owner.book.room') }}">
                                <i class="menu-icon la la-hand-o-right"></i>
                                <span class="menu-title">@lang('Book Room')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.extra.service.add')
                        <li class="sidebar-menu-item {{ menuActive('owner.extra.service.*') }}">
                            <a class="nav-link" href="{{ route('owner.extra.service.add') }}">
                                <i class="menu-icon las la-plus-circle"></i>
                                <span class="menu-title">@lang('Add Service')</span>
                            </a>
                        </li>
                    @endcan
                    @can($manageBookingMenus)
                        <li class="sidebar-menu-item sidebar-dropdown">
                            <a class="{{ menuActive($manageBookingMenus, 3) }}" href="javascript:void(0)">
                                <i class="menu-icon las la-list"></i>
                                <span class="menu-title">@lang('Manage Bookings')</span>
                                @if ($delayedCheckoutCount || $refundableBookingCount)
                                    <span class="menu-badge pill bg--danger ms-auto">
                                        <i class="fa fa-exclamation"></i>
                                    </span>
                                @endif
                            </a>
                            <div class="sidebar-submenu {{ menuActive($manageBookingMenus, 2) }}">
                                <ul>
                                    @can('owner.booking.active')
                                        <li class="sidebar-menu-item {{ menuActive('owner.booking.active') }}">
                                            <a class="nav-link" href="{{ route('owner.booking.active') }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Active Bookings')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('owner.booking.checked.out.list')
                                        <li class="sidebar-menu-item {{ menuActive('owner.booking.checked.out.list') }}">
                                            <a class="nav-link" href="{{ route('owner.booking.checked.out.list') }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Checked Out Bookings')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('owner.booking.canceled.list')
                                        <li class="sidebar-menu-item {{ menuActive('owner.booking.canceled.list') }}">
                                            <a class="nav-link" href="{{ route('owner.booking.canceled.list') }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Canceled Bookings')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('owner.booking.refundable')
                                        <li class="sidebar-menu-item {{ menuActive('owner.booking.refundable') }}">
                                            <a class="nav-link" href="{{ route('owner.booking.refundable') }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Refundable Bookings')</span>
                                                @if ($refundableBookingCount)
                                                    <span class="menu-badge pill bg--danger ms-auto">
                                                        {{ $refundableBookingCount }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan
                                    @can('owner.booking.checkout.delayed')
                                        <li class="sidebar-menu-item {{ menuActive('owner.booking.checkout.delayed') }}">
                                            <a class="nav-link" href="{{ route('owner.booking.checkout.delayed') }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Delayed Checkout')</span>
                                                @if ($delayedCheckoutCount)
                                                    <span class="menu-badge pill bg--danger ms-auto">
                                                        {{ $delayedCheckoutCount }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan
                                    @can('owner.booking.all')
                                        <li class="sidebar-menu-item {{ menuActive('owner.booking.all') }}">
                                            <a class="nav-link" href="{{ route('owner.booking.all') }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('All Bookings')</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcan
                @endcan
                @can(['owner.ticket.index', 'owner.ticket.open', 'owner.ticket.view', 'owner.deposit.index',
                    'owner.withdraw', 'owner.withdraw.history'])
                    @can('owner.ticket.index')
                        <li class="sidebar__menu-header">@lang('Others')</li>
                        <li
                            class="sidebar-menu-item {{ menuActive(['owner.ticket.index', 'owner.ticket.open', 'owner.ticket.view']) }}">
                            <a class="nav-link" href="{{ route('owner.ticket.index') }}">
                                <i class="menu-icon la la-ticket-alt"></i>
                                <span class="menu-title">@lang('Support tickets')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.deposit.index')
                        <li class="sidebar-menu-item {{ menuActive(['owner.deposit.index', 'owner.payment.history']) }}">
                            <a class="nav-link" href="{{ route('owner.deposit.index') }}">
                                <i class="menu-icon las la-money-bill"></i>
                                <span class="menu-title">@lang('Subscription')</span>
                            </a>
                        </li>
                    @endcan
                    @can(['owner.withdraw', 'owner.withdraw.history'])
                        <li class="sidebar-menu-item {{ menuActive('owner.withdraw*') }}">
                            <a class="nav-link" href="{{ route('owner.withdraw') }}">
                                <i class="menu-icon las la-wallet"></i>
                                <span class="menu-title">@lang('Withdraw')</span>
                            </a>
                        </li>
                    @endcan
                @endcan
                @can(['owner.report*'])
                    <li class="sidebar__menu-header">@lang('Report')</li>
                    @can('owner.report.bookings')
                        <li class="sidebar-menu-item {{ menuActive('owner.report.bookings') }}">
                            <a class="nav-link" href="{{ route('owner.report.bookings') }}">
                                <i class="menu-icon las la-list-alt"></i>
                                <span class="menu-title">@lang('Bookings')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.report.action.history')
                        <li class="sidebar-menu-item {{ menuActive('owner.report.action.history') }}">
                            <a class="nav-link" href="{{ route('owner.report.action.history') }}">
                                <i class="menu-icon las la-history"></i>
                                <span class="menu-title">@lang('Booking Actions')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.report.transaction')
                        <li class="sidebar-menu-item {{ menuActive('owner.report.transaction') }}">
                            <a class="nav-link" href="{{ route('owner.report.transaction') }}">
                                <i class="menu-icon las la-exchange-alt"></i>
                                <span class="menu-title">@lang('Transaction')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.report.payments.received')
                        <li class="sidebar-menu-item {{ menuActive('owner.report.payments.received') }}">
                            <a class="nav-link" href="{{ route('owner.report.payments.received') }}">
                                <i class="menu-icon las la-money-bill"></i>
                                <span class="menu-title">@lang('Received Payments')</span>
                            </a>
                        </li>
                    @endcan
                    @can('owner.report.payments.returned')
                        <li class="sidebar-menu-item {{ menuActive('owner.report.payments.returned') }}">
                            <a class="nav-link" href="{{ route('owner.report.payments.returned') }}">
                                <i class="menu-icon las la-money-check"></i>
                                <span class="menu-title">@lang('Returned Payments')</span>
                            </a>
                        </li>
                    @endcan
                @endcan
            </ul>
        </div>
    </div>
</div>

@push('style')
    <style>
        .transform-rotate-180 {
            transform: rotate(180deg)
        }
    </style>
@endpush

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
