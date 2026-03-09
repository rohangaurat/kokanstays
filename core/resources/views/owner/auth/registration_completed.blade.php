@extends('owner.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/owner/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">
                                    @lang('Welcome to') <strong>{{ __(gs()->site_name) }}</strong>
                                </h3>
                                <p class="text-white">@lang('Registration Completed')</p>
                            </div>
                            <div class="login-wrapper__body">
                                <img alt="@lang('congrats')" src="{{ getImage('assets/images/congrats.png') }}" />
                                <h2 class="text--white">@lang('Congratulations')</h2>
                                <p class="text--white">@lang('Your registration process has been completed. Please wait for admin response.')</p>
                                <div class="text-white mt-3">
                                    <a href="{{ route('owner.login') }}" class="text-white mt-4">
                                        <i class="las la-sign-in-alt" aria-hidden="true"></i>
                                        @lang('Login your account')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .login-wrapper__body{
            text-align: center;
        }
        .login-wrapper__body img {
            max-width: 200px;
            margin-bottom: 30px;
        }
        .login-wrapper__body h2{
            font-weight: 600;
        }
        .login-wrapper__body p{
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
@endpush
