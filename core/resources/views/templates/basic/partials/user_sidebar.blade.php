@php $user = auth()->user(); @endphp
<div class="sidebar-menu flex-between">
    <div class="sidebar-menu__inner">
        <span class="sidebar-menu__close d-lg-none d-block"><i class="fas fa-times"></i></span>
        <div class="my-profile">
            <div class="my-profile__top">
                <div class="thumb">
                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, isAvatar: true) }}"
                        alt="profile image">
                </div>
            </div>
            <div class="my-profile__content">
                <h6 class="my-profile__name">{{ __($user->fullname) }}</h6>
                <p class="my-profile__mail">{{ $user->email }}</p>
            </div>
        </div>
        <ul class="sidebar-menu-list">
            <li class="sidebar-menu-list__item {{ menuActive(['user.booking.history', 'user.booking.details']) }}">
                <a href="{{ route('user.booking.history') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-bed"></i></span>
                    <span class="text">@lang('My Booking')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item {{ menuActive('user.booking.request.*') }}">
                <a href="{{ route('user.booking.request.history') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-archway"></i></span>
                    <span class="text">@lang('Booking Request')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item {{ menuActive('user.deposit.history') }}">
                <a href="{{ route('user.deposit.history') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-file-invoice-dollar"></i></span>
                    <span class="text">@lang('Payment History')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item {{ menuActive('user.transactions') }}">
                <a href="{{ route('user.transactions') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-exchange-alt"></i></span>
                    <span class="text">@lang('Transactions')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item {{ menuActive('ticket.*') }}">
                <a href="{{ route('ticket.index') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-headset"></i></span>
                    <span class="text">@lang('Support Tickets')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item {{ menuActive('user.profile.setting') }}">
                <a href="{{ route('user.profile.setting') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-user-cog"></i></span>
                    <span class="text">@lang('Profile Setting')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item {{ menuActive('user.change.password') }}">
                <a href="{{ route('user.change.password') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-key"></i></span>
                    <span class="text">@lang('Change Password')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item">
                <a href="{{ route('user.logout') }}" class="sidebar-menu-list__link text--danger">
                    <span class="icon"><i class="las la-sign-out-alt"></i></span>
                    <span class="text">@lang('Logout')</span>
                </a>
            </li>
        </ul>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            'use strict';

            $('[name="image"]').on('change', function() {
                var form = $(this).closest('form');
                form.submit();
            });
        })(jQuery);
    </script>
@endpush
