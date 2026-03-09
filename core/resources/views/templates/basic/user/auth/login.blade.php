@extends('Template::layouts.frontend')
@php $userLoginContent = getContent('user_login.content', true); @endphp
@section('content')
    <section class="account py-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-4 col-md-7 col-sm-10">
                    <div class="account-form">
                        @if ($userLoginContent ?? false)
                            <div class="account-form__content">
                                <h5 class="account-form__title">
                                    {{ __($userLoginContent->data_values->heading ?? '') }}
                                </h5>
                                <p class="account-form__desc">
                                    {{ __($userLoginContent->data_values->subheading ?? '') }}
                                </p>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('Username')</label>
                                <input type="text" class="form--control" name="username"
                                    value="{{ old('username') }}"  required>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Password')</label>
                                <div class="position-relative">
                                    <input type="password" name="password" class="form-control form--control" required>
                                    <span class="password-show-hide fa-solid fa-eye-slash toggle-password "
                                        id="#password"></span>
                                </div>
                            </div>

                            @php $class = 'form--label label-two'; @endphp
                            <x-captcha :class="$class" />

                            <div class="form-group d-flex justify-content-between align-items-center gap-2">
                                <div class="form--check">
                                    <input class="form-check-input" type="checkbox" id="remember"
                                        @checked(old('remember')) name="remember">
                                    <label for="remember" class="form-check-label">
                                        @lang('Remember Me')
                                    </label>
                                </div>
                                <a href="{{ route('user.password.request') }}" class="form--label text--base m-0">
                                    @lang('Forgot Password?')
                                </a>
                            </div>
                            <button type="submit" class="btn btn--base w-100 btn--lg">@lang('Login')</button>
                            @if (gs('registration'))
                                <div class="have-account mt-2">
                                    <p class="have-account__text">
                                        @lang('Don\'t have an account?')
                                        <a href="{{ route('user.register') }}" class="have-account__link text--base">
                                            @lang('Create account')
                                        </a>
                                    </p>
                                </div>
                            @endif
                            @include('Template::partials.social_login', ['pageAction' => 'Login'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
