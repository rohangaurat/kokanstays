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
                                @lang('To recover your account please provide your email or username to find your account.')
                            </p>
                        </div>
                        <form method="POST" action="{{ route('user.password.email') }}" class="verify-gcaptcha">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="form--label">@lang('Email or Username')</label>
                                    <input type="text" class="form--control" name="value" value="{{ old('value') }}"
                                        required autofocus="off">
                                </div>
                                <div class="col-12">
                                    @php $class = 'form--label label-two'; @endphp
                                    <x-captcha :class="$class" />
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
