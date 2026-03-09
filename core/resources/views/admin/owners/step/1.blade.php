<div class="row">
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label> @lang('Name')</label>
            <input class="form-control" name="name" required type="text" value="{{ old('name', @$setting->name) }}">
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label> @lang('Star Rating')</label>
            <select class="form-control" name="star_rating" required>
                <option disabled selected value="">@lang('Select One')</option>
                @for ($i = 1; $i <= gs()->max_star_rating; $i++)
                    <option @selected(old('star_rating', @$setting->star_rating) == $i) value="{{ $i }}">{{ $i }}
                        @lang('Star')</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label> @lang('Country')</label>
            <select class="select2 allCountries" name="country_id" required>
                <option disabled selected value="">@lang('Select One')</option>
                @foreach ($countries as $country)
                    <option @selected(old('country_id', @$setting->country_id) == $country->id) data-cities="{{ $country->cities }}"
                        value="{{ $country->id }}">{{ __($country->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label>@lang('City')</label>
            <select class="select2 allCities" name="city_id" required>
                <option disabled selected value="">@lang('Select Country First')</option>
            </select>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label>@lang('Location')</label>
            <select class="select2 allLocations" name="location_id" required>
                <option disabled selected value="">@lang('Select City First')</option>
            </select>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label> @lang('Address')</label>
            <input class="form-control" name="hotel_address" required type="text"
                value="{{ old('hotel_address', @$setting->hotel_address) }}">
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label> @lang('Latitude')</label>
            <input class="form-control" name="latitude" required step="any" type="number"
                value="{{ old('latitude', $setting->latitude ?? '') }}">
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label> @lang('Longitude')</label>
            <input class="form-control" name="longitude" required step="any" type="number"
                value="{{ old('longitude', $setting->longitude ?? '') }}">
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label>@lang('Tax Name')</label>
            <input class="form-control" name="tax_name" required type="text"
                value="{{ old('tax_name', $setting->tax_name) }}">
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label>@lang('Tax Percent Charge')</label>
            <div class="input-group">
                <input class="form-control" min="0" name="tax_percentage" required step="any" type="number"
                    value="{{ old('tax_percentage', $setting->tax_percentage) }}">
                <span class="input-group-text">%</span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label> @lang('Check In Time')</label>
            <div class="input-group">
                <input autocomplete="off" class="form-control" name="checkin_time" placeholder="--:--" required
                    type="time"
                    value="{{ old('checkin_time', $setting->checkin_time ? showDateTime($setting->checkin_time, 'H:i') : '') }}">
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 col-xxl-4">
        <div class="form-group">
            <label> @lang('Checkout Time')</label>
            <div class="input-group">
                <input autocomplete="off" class="form-control" name="checkout_time" placeholder="--:--" required
                    type="time"
                    value="{{ old('checkout_time', @$setting->checkout_time ? showDateTime(@$setting->checkout_time, 'H:i') : '') }}">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>
                @lang('Upcoming Check-In List')
                <i class="las la-info-circle" title="@lang('The number of days of data you want to see in the upcoming check-in list.')"></i>
            </label>
            <div class="input-group">
                <input class="form-control" min="1" name="upcoming_checkin_days" required type="number"
                    value="{{ old('upcoming_checkin_days', $setting->upcoming_checkin_days) }}">
                <span class="input-group-text">@lang('Days')</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>
                @lang('Upcoming Checkout List')
                <i class="las la-info-circle" title="@lang('The number of days of data you want to see in the upcoming checkout list.')"></i>
            </label>
            <div class="input-group">
                <input class="form-control" min="1" name="upcoming_checkout_days" required type="number"
                    value="{{ old('upcoming_checkout_days', $setting->upcoming_checkout_days) }}">
                <span class="input-group-text">@lang('Days')</span>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <label>@lang('Description')</label>
            <textarea class="form-control" name="description" required rows="10">{{ old('description', $setting->description) }}</textarea>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.allCountries').on('change', function() {
                let cities = $(this).find('option:selected').data('cities');
                if (cities != undefined && cities.length > 0) {
                    var options = `<option value="" selected disabled>@lang('Name')</option>`;
                    $.each(cities, function(index, city) {
                        var locationsString = encodeURIComponent(JSON.stringify(city.locations));
                        options +=
                            `<option value="${city.id}" data-locations="${locationsString}">${city.name}</option>`
                    });
                    $('.allCities').html(options);
                    options = `<option value="" selected disabled>@lang('Star Rating')</option>`;
                    $('.allLocations').html(options);
                }
            }).change();

            @if (@$setting || old('city_id'))
                var cityId = "{{ old('city_id', @$setting->city_id) }}";
                $('.allCities').val(cityId);
            @endif

            $('.allCities').on('change', function() {
                let locations = $(this).find('option:selected').data('locations');

                if (locations != undefined) {
                    locations = JSON.parse(decodeURIComponent(locations));
                    if (locations.length > 0) {
                        var options = `<option value="" selected disabled>@lang('Select One')</option>`;
                        $.each(locations, function(index, location) {
                            options += `<option value="${location.id}">${location.name}</option>`
                        });
                        $('.allLocations').html(options);
                    } else {
                        var options = `<option value="" selected disabled>@lang('Select One')</option>`;
                        $('.allLocations').html(options);
                    }
                }
            }).change();

            @if (@$setting || old('location_id'))
                var locationId = "{{ old('location_id', @$setting->location_id) }}";
                $('.allLocations').val(locationId);
            @endif
        })(jQuery);
    </script>
@endpush
