@extends('Template::layouts.frontend')
@section('content')
    <section class="account py-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-4 col-md-7 col-sm-10">
                    <div class="account-form">
                        <div class="account-form__content">
                            <h5 class="account-form__title">{{ __($pageTitle) }}</h5>
                            <p class="account-form__desc">
                                @lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')
                            </p>
                        </div>
                        <form method="POST" action="{{ route('user.password.update') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="form--label">@lang('Password')</label>
                                    <div class="position-relative">
                                        <input type="password"
                                            class="form-control form--control @if (gs('secure_password')) secure-password @endif"
                                            name="password" required>
                                        <span class="password-show-hide fa-solid fa-eye-slash toggle-password"
                                            id="#password"></span>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="form--label">@lang('Confirm Password')</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control form--control"
                                            name="password_confirmation" required>
                                        <span class="password-show-hide fa-solid toggle-password fa-eye-slash"
                                            id="#password_confirmation"></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn--base w-100 btn--lg">@lang('Submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
