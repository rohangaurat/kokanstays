@php
    $frontendPopularCitiesContent = getContent('popular_cities.content', true);
    $popularCities = App\Models\City::active()
        ->popular()
        ->withCount([
            'hotelSettings' => function ($hotelSetting) {
                $hotelSetting->whereHas('owner', function ($owner) {
                    $owner
                        ->active()
                        ->notExpired()
                        ->whereHas('roomTypes', function ($roomTypes) {
                            $roomTypes->where('status', Status::ROOM_TYPE_ACTIVE)->whereHas('rooms', function ($rooms) {
                                $rooms->where('status', Status::ROOM_ACTIVE)->isAvailableRoom();
                            });
                        });
                });
            },
        ])
        ->orderByDesc('hotel_settings_count')
        ->limit(4)
        ->get();
@endphp
@if (!blank($popularCities))
    <div class="location-section pt-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h4 class="section-heading__title">
                            {{ __($frontendPopularCitiesContent?->data_values?->heading ?? '') }}
                        </h4>
                        <p class="section-heading__desc">
                            {{ __($frontendPopularCitiesContent?->data_values?->subheading ?? '') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row gy-4">
                @foreach ($popularCities ?? [] as $city)
                    <div class="col-lg-3 col-sm-6">
                        <div class="location-item">
                            <a href="{{ route('hotel.index') . '?city_id=' . $city->id }}" class="location-item__thumb">
                                <img src="{{ getImage(getFilePath('city') . '/' . $city->image, getFileSize('city')) }}"
                                     alt="city image" class="fit-image">
                            </a>
                            <div class="location-item__content">
                                <span class="location-item__list">
                                    {{ $city->hotel_settings_count ?? 0 }} @lang('Hotels')
                                </span>
                                <h5 class="location-item__title">
                                    <a href="{{ route('hotel.index') . '?city_id=' . $city->id }}"
                                       class="location-item__title-link">
                                        {{ __($city->name) }}
                                    </a>
                                </h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
