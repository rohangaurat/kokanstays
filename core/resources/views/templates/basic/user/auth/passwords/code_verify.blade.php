@extends('Template::layouts.frontend')
@section('content')
    <div class="container py-120">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-5">
                <div class="d-flex justify-content-center">
                    <div class="verification-code-wrapper">
                        <div class="verification-area">
                            <div class="account-form__content">
                                <h5 class="account-form__title">@lang('Verify Email Address')</h5>
                                <p class="account-form__desc">
                                    @lang('A 6 digit verification code sent to your email address'): {{ showEmailAddress($email) }}
                                </p>
                            </div>
                            <form action="{{ route('user.password.verify.code') }}" method="POST" class="submit-form">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                @include('Template::partials.verification_code')
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100 btn--lg">@lang('Submit')</button>
                                </div>
                                <div class="form-group m-0">
                                    @lang('Please check including your Junk/Spam Folder. if not found, you can')
                                    <a href="{{ route('user.password.request') }}">@lang('Try to send again')</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .verification-code-wrapper::after {
            background-color: hsl(var(--white));
        }
    </style>
@endpush
