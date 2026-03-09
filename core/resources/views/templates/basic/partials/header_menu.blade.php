@guest
    <li class="login-registration-list__item">
        <a href="{{ route('user.login') }}" class="login-registration-list__link login">
            <span class="login-registration-list__icon"><i class="las la-file-export"></i></span>
            @lang('Login')
        </a>
    </li>
@endguest
@auth
    <li>
        <div class="user-info">
            <button class="user-info__button p-0">
                <span class="user-info__thumb">
                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image ?? null, isAvatar: true) }}"
                         class="fit-image" alt="profile image">
                </span>
            </button>
            <ul class="user-info-dropdown">
                <li class="user-info-profile mb-2">
                    <span class="user-info-profile__thumb">
                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image ?? null, isAvatar: true) }}"
                             class="fit-image" alt="profile image">
                    </span>
                    <div class="user-info-profile__content">
                        <p class="user-info-profile__name">{{ __($user->fullname) }}</p>
                        <span class="user-info-profile__mail">{{ $user->email }}</span>
                    </div>
                </li>
                <li class="user-info-dropdown__item">
                    <a class="user-info-dropdown__link" href="{{ route('user.profile.setting') }}">
                        <span class="icon"><i class="las la-user-cog"></i></span>
                        <span class="text">@lang('Profile Setting')</span>
                    </a>
                </li>
                <li class="user-info-dropdown__item">
                    <a class="user-info-dropdown__link" href="{{ route('user.booking.history') }}">
                        <span class="icon"><i class="las la-bed"></i></span>
                        <span class="text">@lang('My Booking')</span>
                    </a>
                </li>
                <li class="user-info-dropdown__item">
                    <a class="user-info-dropdown__link" href="{{ route('user.deposit.history') }}">
                        <span class="icon"><i class="las la-file-invoice-dollar"></i></span>
                        <span class="text">@lang('Payment History')</span>
                    </a>
                </li>
                <li class="user-info-dropdown__item ">
                    <a class="user-info-dropdown__link logout" href="{{ route('user.logout') }}">
                        <span class="icon"><i class="las la-sign-out-alt"></i></span>
                        <span class="text">@lang('Logout')</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endauth
