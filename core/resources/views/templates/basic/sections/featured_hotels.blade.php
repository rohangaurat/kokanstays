@php
    $frontendFeaturedHotelsContent = getContent('featured_hotels.content', true);
    $featuredHotels = App\Models\Owner::active()
        ->notExpired()
        ->featured()
        ->whereHas('hotelSetting')
        ->with(['hotelSetting'])
        ->withCount('bookings')
        ->having('bookings_count', '>', 0)
        ->orderByDesc('bookings_count')
        ->limit(4)
        ->get();
@endphp
@if (!blank($featuredHotels))
    <div class="feature-section pt-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading style-left">
                        <div class="flex-between">
                            <h4 class="section-heading__title mb-0">
                                {{ __($frontendFeaturedHotelsContent?->data_values?->heading ?? '') }}
                            </h4>
                            <a href="{{ route('hotel.featured') }}" class="btn btn--base btn--sm">
                                @lang('View all') <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row gy-4">
                @foreach ($featuredHotels ?? [] as $featuredHotel)
                    <div class="col-xl-3 col-sm-6">
                        <div class="card-item">
                            <a href="{{ route('hotel.details', $featuredHotel->id) }}" class="card-item__thumb">
                                <img src="{{ getImage(getFilePath('hotelImage') . '/' . $featuredHotel?->hotelSetting?->image ?? null, getFileSize('hotelImage')) }}"
                                     alt="featured hotel image" class="fit-image">
                            </a>
                            <div class="card-item__content">
                                <h6 class="card-item__title">
                                    <a href="{{ route('hotel.details', $featuredHotel->id) }}"
                                       class="card-item__title-link">
                                        {{ __($featuredHotel?->hotelSetting?->name ?? '') }}
                                    </a>
                                </h6>
                                <p class="card-item__location">
                                    <span class="card-item__icon"><i class="las la-map-marker"></i></span>
                                    {{ __($featuredHotel?->hotelSetting?->location?->name ?? '') }},
                                    {{ __($featuredHotel?->hotelSetting?->city?->name ?? '') }}
                                </p>
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
