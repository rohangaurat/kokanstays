@php
    $frontendPopularHotelsContent = getContent('popular_hotels.content', true);
    $popularHotels = App\Models\Owner::active()
        ->notExpired()
        ->whereHas('hotelSetting')
        ->with([
            'hotelSetting',
            'roomTypes' => function ($roomType) {
                $roomType->active()->orderBy('fare')->take(1);
            },
        ])
        ->withCount([
            'bookings as total_bookings' => function ($query) {
                $query
                    ->where('status', Status::BOOKING_ACTIVE);
                    // ->whereDate('created_at', '>=', Carbon\Carbon::now()->subDays(gs('popularity_count_from')));
            },
        ])
        ->having('total_bookings', '>', 0)
        ->orderByDesc('total_bookings')
        ->limit(4)
        ->get();
@endphp
@if (!blank($popularHotels))
    <div class="popular-section pt-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading style-left">
                        <div class="flex-between">
                            <h4 class="section-heading__title mb-0">
                                {{ __($frontendPopularHotelsContent?->data_values?->heading ?? '') }}
                            </h4>
                            <a href="{{ route('hotel.popular') }}" class="btn btn--base btn--sm">
                                @lang('View all') <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row gy-4">
                @foreach ($popularHotels ?? [] as $popularHotel)
                    <div class="col-xl-3 col-sm-6">
                        <div class="card-item">
                            <a href="{{ route('hotel.details', $popularHotel->id) }}" class="card-item__thumb">
                                <img src="{{ getImage(getFilePath('hotelImage') . '/' . $popularHotel?->hotelSetting?->image ?? null, getFileSize('hotelImage')) }}"
                                    alt="hotel image" class="fit-image">
                            </a>
                            <div class="card-item__content">
                                <h6 class="card-item__title">
                                    <a href="{{ route('hotel.details', $popularHotel->id) }}"
                                        class="card-item__title-link">
                                        {{ __($popularHotel?->hotelSetting?->name ?? '') }}
                                    </a>
                                </h6>
                                <p class="card-item__location">
                                    <span class="card-item__icon"><i class="las la-map-marker"></i></span>
                                    {{ __($popularHotel?->hotelSetting?->location?->name ?? '') }},
                                    {{ __($popularHotel?->hotelSetting?->city?->name ?? '') }},
                                    {{ __($popularHotel?->hotelSetting?->country?->name ?? '') }}
                                </p>
                                <div class="card-item__bottom">
                                    <span class="name">
                                        {{ $popularHotel?->hotelSetting?->star_rating ?? '' }} @lang('Star Hotel')
                                    </span>
                                    <span class="text">
                                        @lang('Starts Form')
                                        <span class="price">
                                            {{ showAmount($popularHotel?->roomTypes[0]?->fare) }}
                                        </span>/@lang('Night')
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@push('style')
    <style>
        .card-item__thumb img{
            max-height: 170px;
        }
    </style>
@endpush
