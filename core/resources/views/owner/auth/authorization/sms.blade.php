@extends('owner.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/admin/images/login.jpg') }}')">
        <div class="container custom-container d-flex justify-content-center">
            <div class="login-area">
                <div class="text-center mb-3">
                    <h2 class="text-white mb-2">@lang('Verify Mobile Number')</h2>
                    <p class="text-white mb-2">@lang('A 6 digit verification code sent to your mobile number'): {{ showMobileNumber(authOwner()->mobile) }}</p>
                </div>
                <form action="{{ route('owner.verify.mobile') }}" class="login-form w-100" method="POST">
                    @csrf

                    <div class="code-box-wrapper d-flex w-100">
                        <div class="form-group mb-3 flex-fill">
                            <span class="text-white fw-bold">@lang('Verification Code')</span>
                            <div class="verification-code">
                                <input autocomplete="off" class="overflow-hidden" name="code" type="text">
                                <div class="boxes">
                                    <span>-</span>
                                    <span>-</span>
                                    <span>-</span>
                                    <span>-</span>
                                    <span>-</span>
                                    <span>-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between">
                        <a class="forget-text" href="{{ route('owner.send.verify.code', 'phone') }}">@lang('Try again')</a>
                    </div>
                    <button class="btn cmn-btn w-100 mt-4" type="submit">@lang('Submit')</button>
                </form>
                @if ($errors->has('resend'))
                    <small class="text-danger d-block mt-2">{{ $errors->first('resend') }}</small>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link href="{{ asset('assets/owner/css/verification_code.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            $('[name=code]').on('input', function() {

                $(this).val(function(i, val) {
                    if (val.length >= 6) {
                        $('form').find('button[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                        $('form').find('button[type=submit]').removeClass('disabled');
                        $('form')[0].submit();
                    } else {
                        $('form').find('button[type=submit]').addClass('disabled');
                    }
                    if (val.length > 6) {
                        return val.substring(0, val.length - 1);
                    }
                    return val;
                });

                for (let index = $(this).val().length; index >= 0; index--) {
                    $($('.boxes span')[index]).html('');
                }
            });

        })(jQuery)
    </script>
@endpush
