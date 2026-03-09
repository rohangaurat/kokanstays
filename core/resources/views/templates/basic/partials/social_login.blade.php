@if (@gs('socialite_credentials')->linkedin->status || @gs('socialite_credentials')->facebook->status || @gs('socialite_credentials')->google->status)
    <p class="account-form__option">
        <span class="text">@lang('OR')</span>
    </p>
    <ul class="social-login-list">
        @if (@gs('socialite_credentials')->google->status)
            <li class="social-login-list__item mb-2">
                <a href="{{ route('user.social.login', 'google') }}" class="social-login-list__link w-100">
                    <span class="icon">
                        <img src="{{ asset(activeTemplate(true) . 'images/google.svg') }}" alt="google">
                    </span>
                    {{ __($pageAction) }} @lang('With Google')
                </a>
            </li>
        @endif
        @if (@gs('socialite_credentials')->facebook->status)
            <li class="social-login-list__item mb-2">
                <a href="{{ route('user.social.login', 'facebook') }}" class="social-login-list__link w-100">
                    <span class="icon">
                        <img src="{{ asset(activeTemplate(true) . 'images/facebook.svg') }}" alt="facebook">
                    </span>
                    {{ __($pageAction) }} @lang('With Facebook')
                </a>
            </li>
        @endif
        @if (@gs('socialite_credentials')->linkedin->status)
            <li class="social-login-list__item">
                <a href="{{ route('user.social.login', 'linkedin') }}" class="social-login-list__link w-100">
                    <span class="icon">
                        <img src="{{ asset(activeTemplate(true) . 'images/linkedin.svg') }}" alt="linkedin">
                    </span>
                    {{ __($pageAction) }} @lang('With Linkedin')
                </a>
            </li>
        @endif
    </ul>
@endif
