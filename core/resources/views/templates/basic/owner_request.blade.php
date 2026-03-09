@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $content = getContent('owner_request.content', true);
        $elements = getContent('owner_request.element', orderById: true);
    @endphp
    <section class="owner-request mt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center gy-4">
                <div class="col-lg-5">
                    <div class="get-facilities pe-lg-5">
                        <div class="section-heading style-left">
                            <h3 class="section-heading__title" s-break="-2">{{ __($content->data_values->heading ?? '') }}</h3>
                            <p class="section-heading__desc">{{ __($content->data_values->subheading ?? '') }}</p>
                        </div>
                        @foreach ($elements as $item)
                            <div class="get-facilities__item">
                                <span class="get-facilities__icon"> @php echo $item->data_values->icon; @endphp </span>
                                <div class="get-facilities__conent">
                                    <h6 class="get-facilities__title">{{ __($item->data_values->title) }}</h6>
                                    <p class="get-facilities__desc">{{ __($item->data_values->description) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="owner-form @if (!gs('is_enable_owner_request')) form-disabled @endif">
                        <div class="card custom--card custom--card--lg">
                            @if (!gs('is_enable_owner_request'))
                                <span class="form-disabled-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="80" height="80" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                        <g>
                                            <path d="M255.999 0c-79.044 0-143.352 64.308-143.352 143.353v70.193c0 4.78 3.879 8.656 8.659 8.656h48.057a8.657 8.657 0 0 0 8.656-8.656v-70.193c0-42.998 34.981-77.98 77.979-77.98s77.979 34.982 77.979 77.98v70.193c0 4.78 3.88 8.656 8.661 8.656h48.057a8.657 8.657 0 0 0 8.656-8.656v-70.193C399.352 64.308 335.044 0 255.999 0zM382.04 204.89h-30.748v-61.537c0-52.544-42.748-95.292-95.291-95.292s-95.291 42.748-95.291 95.292v61.537h-30.748v-61.537c0-69.499 56.54-126.04 126.038-126.04 69.499 0 126.04 56.541 126.04 126.04v61.537z" fill="rgb(0 0 0 / 60%)" opacity="1" data-original="rgb(0 0 0 / 60%)" class=""></path>
                                            <path d="M410.63 204.89H101.371c-20.505 0-37.188 16.683-37.188 37.188v232.734c0 20.505 16.683 37.188 37.188 37.188H410.63c20.505 0 37.187-16.683 37.187-37.189V242.078c0-20.505-16.682-37.188-37.187-37.188zm19.875 269.921c0 10.96-8.916 19.876-19.875 19.876H101.371c-10.96 0-19.876-8.916-19.876-19.876V242.078c0-10.96 8.916-19.876 19.876-19.876H410.63c10.959 0 19.875 8.916 19.875 19.876v232.733z" fill="rgb(0 0 0 / 60%)" opacity="1" data-original="rgb(0 0 0 / 60%)" class=""></path>
                                            <path d="M285.11 369.781c10.113-8.521 15.998-20.978 15.998-34.365 0-24.873-20.236-45.109-45.109-45.109-24.874 0-45.11 20.236-45.11 45.109 0 13.387 5.885 25.844 16 34.367l-9.731 46.362a8.66 8.66 0 0 0 8.472 10.436h60.738a8.654 8.654 0 0 0 8.47-10.434l-9.728-46.366zm-14.259-10.961a8.658 8.658 0 0 0-3.824 9.081l8.68 41.366h-39.415l8.682-41.363a8.655 8.655 0 0 0-3.824-9.081c-8.108-5.16-12.948-13.911-12.948-23.406 0-15.327 12.469-27.796 27.797-27.796 15.327 0 27.796 12.469 27.796 27.796.002 9.497-4.838 18.246-12.944 23.403z" fill="rgb(0 0 0 / 60%)" opacity="1" data-original="rgb(0 0 0 / 60%)" class=""></path>
                                        </g>
                                    </svg>
                                </span>
                            @endif

                            <div class="card-header bg-transparent">
                                <h4 class="title fw-bold mb-2">{{ __($content->data_values->form_title ?? '') }}</h4>
                                <p class="desc fs-14">{{ __($content->data_values->form_subtitle ?? '') }}</p>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('vendor.request.send') }}" method="POST" class="verify-gcaptcha">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Hotel Name')</label>
                                                <input type="text" class="form--control" name="hotel_name" value="{{ old('hotel_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Star Rating')</label>
                                                <select name="star_rating" class="form-select form--control">
                                                    <option value="" selected disabled>@lang('Select One')</option>
                                                    @for ($i = 1; $i <= gs()->max_star_rating; $i++)
                                                        <option value="{{ $i }}" @selected(old('star_rating') == $i)>{{ $i }} @lang('Star')</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Country')</label>
                                                <select name="country" class="form-select form--control" required>
                                                    <option value="" selected disabled>@lang('Select One')</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}" data-code="{{ $country->code }}" data-mobile_code="{{ $country->dial_code }}" @selected(old('country') == $country->id)>{{ __($country->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('City')</label>
                                                <select name="city" class="form-select form--control" required>
                                                    <option value="" selected disabled>@lang('Select Country First')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Location')</label>
                                                <select name="location" class="form-select form--control" required>
                                                    <option value="" selected disabled>@lang('Select City First')</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Vendor First Name')</label>
                                                <input type="text" name="firstname" class="form--control" value="{{ old('firstname') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Vendor Last Name')</label>
                                                <input type="text" name="lastname" class="form--control" value="{{ old('lastname') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <input type="hidden" name="country_code" value="{{ old('country_code') }}">
                                            <input type="hidden" name="mobile_code" value="{{ old('mobile_code') }}">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Mobile')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text mobileCode"></span>
                                                    <input type="number" name="mobile" class="form-control form--control checkUser" value="{{ old('mobile') }}" required>
                                                </div>
                                                <small class="text--danger mobileExist"></small>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            @php
                                                $email = old('email');
                                                if (request()->email) {
                                                    $email = request()->email;
                                                }
                                            @endphp
                                            <div class="form-group">
                                                <label class="form-label">@lang('Email')</label>
                                                <input type="email" name="email" class="form--control checkUser" value="{{ $email }}" required>
                                                <small class="text--danger emailExist"></small>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <x-captcha />
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn--base w-100">@lang('Send Request')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        "use strict";
        (function($) {
            let cities = @json($cities);
            let locations = @json($locations);

            $('select[name=country]').on('change',function() {
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

            $('.checkUser').on('focusout', function(e) {
                var url = "{{ route('vendor.check.user') }}";
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobileCode').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush

@if (!gs('is_enable_owner_request'))
    @push('style')
        <style>
            /* new css */
            .form-disabled {
                overflow: hidden;
                position: relative;
            }

            .form-disabled::after {
                content: "";
                position: absolute;
                height: 100%;
                width: 100%;
                background-color: rgba(255, 255, 255, 0.2);
                top: 0;
                left: 0;
                backdrop-filter: blur(2px);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
                z-index: 99;
            }

            .form-disabled-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 991;
                font-size: 24px;
                height: auto;
                width: 100%;
                text-align: center;
                color: #000;
                font-weight: 800;
                line-height: 1.2;
            }
        </style>
    @endpush
@endif
