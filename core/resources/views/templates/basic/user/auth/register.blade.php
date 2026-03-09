@if (gs('registration'))
    @extends('Template::layouts.frontend')
    @php $userRegistrationContent = getContent('user_registration.content', true); @endphp
    @section('content')
        <section class="account py-80">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-md-7 col-sm-10">
                        <div class="account-form">
                            @if ($userRegistrationContent ?? false)
                                <div class="account-form__content">
                                    <h5 class="account-form__title">
                                        {{ __($userRegistrationContent?->data_values?->heading ?? '') }}
                                    </h5>
                                    <p class="account-form__desc">
                                        {{ __($userRegistrationContent?->data_values?->subheading ?? '') }}
                                    </p>
                                </div>
                            @endif
                            <form action="{{ route('user.register') }}" method="POST"
                                  class="verify-gcaptcha disableSubmission">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <label class="form--label">@lang('First Name')</label>
                                        <input type="text" class="form--control" name="firstname"
                                               value="{{ old('firstname') }}" required>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label class="form--label">@lang('Last Name')</label>
                                        <input type="text" class="form--control" name="lastname"
                                               value="{{ old('lastname') }}" required>
                                    </div>
                                    <div class="col-12 form-group">
                                        <label class="form--label">@lang('Email Address')</label>
                                        <input type="text" class="form--control checkUser" name="email"
                                               value="{{ old('email') }}" required>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label class="form--label">@lang('Password')</label>
                                        <div class="position-relative">
                                            <input type="password"
                                                   class="form-control form--control @if (gs('secure_password')) secure-password @endif"
                                                   name="password" required>
                                            <span class="password-show-hide fa-solid fa-eye-slash toggle-password"
                                                  id="#password"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label class="form--label">@lang('Confirm Password')</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control form--control"
                                                   name="password_confirmation" required>
                                            <span class="password-show-hide fa-solid toggle-password fa-eye-slash"
                                                  id="#password_confirmation"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @php $class = 'form--label label-two'; @endphp
                                        <x-captcha :class="$class" />
                                    </div>
                                    @if (gs('agree'))
                                        @php $policyPages = getContent('policy_pages.element', false, orderById: true); @endphp
                                        <div class="col-sm-12 form-group">
                                            <div class="form--check">
                                                <input class="form-check-input" type="checkbox" id="agree"
                                                       @checked(old('agree')) name="agree" required>
                                                <label for="agree" class="form-check-label">
                                                    @lang('I agree with')
                                                    @foreach ($policyPages as $policy)
                                                        <a href="{{ route('policy.pages', $policy->slug) }}"
                                                           class="link text--base" target="_blank">
                                                            {{ __($policy->data_values->title) }}
                                                        </a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-sm-12 form-group">
                                        <button type="submit" class="btn btn--base btn--lg w-100">
                                            @lang('Create Account')
                                        </button>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="have-account">
                                            <p class="have-account__text">
                                                @lang('Already have an account? ')
                                                <a href="{{ route('user.login') }}" class="have-account__link text--base">
                                                    @lang('Login')
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @include('Template::partials.social_login', ['pageAction' => 'Register'])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade custom--modal" id="existModalCenter">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header m-0">
                        <h6 class="modal-title" id="existModalLongTitle">@lang('Registration Alert!')</h6>
                        <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </span>
                    </div>
                    <div class="modal-body">
                        <div class="payment-information my-4">
                            <p class="text-center m-0">@lang('You already have an account please login.')</p>
                        </div>
                    </div>
                    <div class="modal-footer p-0 pt-3">
                        <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">
                            @lang('Close')
                        </button>
                        <a href="{{ route('user.login') }}" class="btn btn--base btn--sm">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
@endif

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                var data = {
                    email: value,
                    _token: token
                }
                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
