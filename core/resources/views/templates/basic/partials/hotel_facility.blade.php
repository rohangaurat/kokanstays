@php
    $amenities = $hotel->hotelSetting->amenities()->get();
    $services = $hotel->extraServices()->active()->get();
    $complements = $hotel->hotelSetting->complements;
@endphp
@if (!blank($facilities) || !blank($complements) || !blank($amenities) || !blank($services))
    <div class="hotel-details__item widget_component-wrapper" id="scrollHeadingThree">
        <h5 class="title skeleton">@lang('All Facilities')</h5>
        <div class="facility-wrapper">
            @if (!blank($facilities))
                <div class="facility-item">
                    <h6 class="facility-item__title skeleton">
                        <span class="icon"><i class="las la-user"></i></span> @lang('Facilities')
                    </h6>
                    <ul class="facility-list">
                        @foreach ($facilities as $facility)
                            <li class="facility-list__item skeleton">{{ __($facility->name ?? '') }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (!blank($complements))
                <div class="facility-item">
                    <h6 class="facility-item__title skeleton">
                        <span class="icon"> <i class="las la-coffee"></i></span> @lang('Complements')
                    </h6>
                    <ul class="facility-list">
                        @foreach ($complements as $complement)
                            <li class="facility-list__item skeleton">{{ __($complement ?? '') }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (!blank($amenities))
                <div class="facility-item">
                    <h6 class="facility-item__title skeleton">
                        <span class="icon"> <i class="las la-bed"></i></span>@lang('Amenities')
                    </h6>
                    <ul class="facility-list">
                        @foreach ($amenities as $amenity)
                            <li class="facility-list__item skeleton">{{ __($amenity->title ?? '') }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (!blank($services))
                <div class="facility-item">
                    <h6 class="facility-item__title skeleton">
                        <span class="icon"> <i class="las la-bed"></i></span>@lang('Services')
                    </h6>
                    <ul class="facility-list">
                        @foreach ($services as $service)
                            <li class="facility-list__item skeleton">{{ __($service->name ?? '') }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endif
