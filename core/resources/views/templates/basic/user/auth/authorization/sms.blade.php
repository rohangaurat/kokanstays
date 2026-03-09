@extends('Template::layouts.frontend')
@section('content')
    <div class="container py-120">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-5">
                <div class="d-flex justify-content-center">
                    <div class="verification-code-wrapper">
                        <div class="verification-area">
                            <div class="account-form__content">
                                <h5 class="account-form__title">@lang('Verify Mobile Number')</h5>
                                <p class="account-form__desc">
                                    @lang('A 6 digit verification code sent to your mobile number'):
                                    +{{ showMobileNumber($user->mobileNumber) }}
                                </p>
                            </div>
                            <form action="{{ route('user.verify.mobile') }}" method="POST" class="submit-form">
                                @csrf
                                @include('Template::partials.verification_code')
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100 btn--lg">@lang('Submit')</button>
                                </div>
                                <div class="form-group m-0">
                                    <p>
                                        @lang('If you don\'t get any code'),
                                        <span class="countdown-wrapper d-inline">
                                            @lang('please try again after')
                                            <span id="countdown" class="fw-bold">--</span>
                                            @lang('seconds')
                                        </span>
                                        <a href="{{ route('user.send.verify.code', 'email') }}"
                                           class="try-again-link d-none">
                                            @lang('Try again')
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var distance = Number(
            "{{ isset($user->ver_code_send_at) ? $user->ver_code_send_at->addMinutes(2)->timestamp - time() : '' }}");
        var x = setInterval(function() {
            distance--;
            document.getElementById("countdown").innerHTML = distance;
            if (distance <= 0) {
                clearInterval(x);
                document.querySelector('.countdown-wrapper').classList.add('d-none');
                document.querySelector('.try-again-link').classList.remove('d-none');
            }
        }, 1000);
    </script>
@endpush


@push('style')
    <style>
        .verification-code-wrapper::after {
            background-color: hsl(var(--white));
        }
    </style>
@endpush
