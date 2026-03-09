@extends('owner.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/owner/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">
                                    @lang('Welcome to') <strong>{{ __(gs()->site_name) }}</strong>
                                </h3>
                                <p class="text-white">{{ __($pageTitle) }}</p>
                            </div>
                            <div class="login-wrapper__body">
                                <form action="{{ route('owner.registration.request.send') }}" method="POST"
                                    class="cmn-form mt-30 verify-gcaptcha login-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Hotel Name')</label>
                                                <input type="text" class="form-control" name="hotel_name"
                                                    value="{{ old('hotel_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Star Rating')</label>
                                                <select name="star_rating" class="form-control select2"
                                                    data-minimum-results-for-search="-1" required>
                                                    <option value="" selected disabled>@lang('Select One')</option>
                                                    @for ($i = 1; $i <= gs()->max_star_rating; $i++)
                                                        <option value="{{ $i }}" @selected(old('star_rating') == $i)>
                                                            {{ $i }} @lang('Star')</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('First Name')</label>
                                                <input type="text" name="firstname" class="form-control"
                                                    value="{{ old('firstname') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Last Name')</label>
                                                <input type="text" name="lastname" class="form-control"
                                                    value="{{ old('lastname') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>@lang('Email')</label>
                                                <input type="email" name="email" class="form-control checkVendor"
                                                    value="{{ old('email', request('email')) }}" required>
                                                <small class="text--danger emailExist"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Country')</label>
                                                <select name="country" class="form-control select2" required>
                                                    <option value="" selected disabled>@lang('Select One')</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}"
                                                            data-code="{{ $country->code }}"
                                                            data-mobile_code="{{ $country->dial_code }}"
                                                            @selected(old('country') == $country->id)>{{ __($country->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="hidden" name="country_code" value="{{ old('country_code') }}">
                                            <input type="hidden" name="mobile_code" value="{{ old('mobile_code') }}">
                                            <div class="form-group">
                                                <label>@lang('Mobile')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text mobileCode"></span>
                                                    <input type="number" name="mobile" class="form-control checkVendor"
                                                        value="{{ old('mobile') }}" required>
                                                </div>
                                                <small class="text--danger mobileExist"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('City')</label>
                                                <select name="city" class="form-control select2" required>
                                                    <option value="" selected disabled>@lang('Select Country First')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Location')</label>
                                                <select name="location" class="form-control select2" required>
                                                    <option value="" selected disabled>@lang('Select City First')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <x-captcha />
                                        </div>
                                        <div class="col-12">
                                            <button type="submit"
                                                class="btn cmn-btn mt-2 w-100">@lang('Send Request')</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="text-center text-white mt-3">
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

@push('script')
    <script>
        (function($) {
            "use strict";

            let cities = @json($cities);
            let locations = @json($locations);

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobileCode').text('+' + $('select[name=country] :selected').data('mobile_code'));

                let countryId = $(this).val();
                let options = `<option value="" selected disabled>@lang('Select One')</option>`;
                $.each(cities, function(index, city) {
                    if (city.country_id == countryId) {
                        options += `<option value="${city.id}">${city.name}</option>`;
                    }
                });

                $('select[name=city]').html(options);
            });

            $('select[name=city]').on('change', function() {
                let cityId = $(this).val();
                let options = `<option value="" selected disabled>@lang('Select One')</option>`;
                $.each(locations, function(index, location) {
                    if (location.city_id == cityId) {
                        options += `<option value="${location.id}">${location.name}</option>`;
                    }
                });
                $('select[name=location]').html(options);
            });


            var mobileCode = @json($mobileCode);

            if (mobileCode != null && $(`option[data-mobile_code="${mobileCode}"]`).length > 0) {
                $(`option[data-mobile_code=${mobileCode}]`).attr('selected', '');
            } else {
                $('select[name=country]').find('option:nth-child(2)').attr('selected', true);
            }

            $('select[name=country]').trigger("change");

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobileCode').text('+' + $('select[name=country] :selected').data('mobile_code'));


            @if (old('city'))
                $('select[name=country]').trigger("change");
                var cityId = "{{ old('city') }}";
                $('select[name=city]').val(cityId);
            @endif

            @if (old('location'))
                $('select[name=city]').trigger("change");
                var locationId = "{{ old('location') }}";
                $('select[name=location]').val(locationId);
            @endif

            $('.checkVendor').on('focusout', function(e) {
                var url = "{{ route('owner.check.user') }}";
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobileCode').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        type: 'mobile',
                        _token: token
                    };
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        type: 'email',
                        _token: token
                    };
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });

            $('.select2').select2({
                dropdownParent: $('.input-group'),
            });

            $('.select2-container').each(function(i) {
                $(this).css({
                    '--index': 999 - i,
                });

            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .form-control {
            background-color: transparent !important;
        }

        .login-wrapper {
            overflow: unset;
        }

        .select2-container--default .select2-selection--single {
            background: #1e157d;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff;
            font-size: 0.875rem;
        }

        .select2-dropdown {
            background: #1e1297;
            color: #fff;
        }

        .select2-container--default .select2-results__option--disabled,
        .select2-results__options li {
            color: #cfcece;
        }

        .select2-results__option--selectable {
            color: #eee
        }


        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        .select2-container--open .select2-selection {
            border: 1px solid #3D2BFB !important;
            box-shadow: 0 3px 9px rgba(50, 50, 9, 0.05), 3px 4px 8px rgba(115, 103, 240, 0.1);
        }

        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            line-height: 50px;
        }

        .select2-search__field {
            color: #fff;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 5px;
        }

        .input-group-text {
            background-color: #3d2bfb;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: #fff;
        }

        .login-form .input-group-text+.form-control {
            border-left: none;
        }

        .select2-results__option.select2-results__option--selected {
            background-color: #3d2bfb;
        }

        .select2-dropdown {
            margin-left: 1px;
        }

        .select2-container {
            z-index: var(--index) !important;
        }
    </style>
@endpush
