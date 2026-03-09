@extends('Template::layouts.frontend')
@section('content')
    <section class="account py-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 col-sm-10">
                    <div class="account-form">
                        <div class="account-form__content">
                            <h5 class="account-form__title">
                                {{ __($pageTitle) }}
                            </h5>
                        </div>
                        <form action="{{ route('user.data.submit') }}" method="POST" class="disableSubmission">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form--label">@lang('Username')</label>
                                    <input type="text" class="form--control checkUser" name="username"
                                           value="{{ old('username') }}" required>
                                    <small class="text--danger usernameExist"></small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form--label">@lang('Mobile')</label>
                                    <div class="phone-number">
                                        <select class="form--control select2" name="country" required>
                                            @foreach ($countries as $key => $country)
                                                <option data-mobile_code="{{ $country->dial_code }}"
                                                        value="{{ old('country', $country->country) }}"
                                                        data-code="{{ $key }}"
                                                        data-img="{{ asset('assets/images/country/' . strtolower($key) . '.svg') }}">
                                                    {{ __($country->dial_code) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="mobile_code">
                                        <input type="hidden" name="country_code">
                                        <input type="number" class="form--control form-control checkUser" name="mobile"
                                               value="{{ old('mobile') }}">
                                    </div>
                                    <small class="text--danger mobileExist"></small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form--label">@lang('Address')</label>
                                    <input type="text" class="form--control" name="address" value="{{ old('address') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form--label">@lang('State')</label>
                                    <input type="text" class="form--control" name="state" value="{{ old('state') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form--label">@lang('Zip Code')</label>
                                    <input type="text" class="form--control" name="zip" value="{{ old('zip') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form--label">@lang('City')</label>
                                    <input type="text" class="form--control" name="city" value="{{ old('city') }}">
                                </div>
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn--base btn--lg w-100">
                                        @lang('Submit')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value, name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));


            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name');
                checkUser(value, name);
            });

            function checkUser(value, name) {
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code: $('[name=mobile_code]').val(),
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.field} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            }
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .select2-container--open .select2-dropdown--below {
            width: 110px !important;
        }

        .phone-number .select2-container {
            width: 110px !important;
        }
    </style>
@endpush
