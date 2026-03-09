@php
    $frontendAdvertisements = App\Models\Advertisement::active()->where('end_date', '>=', date('Y-m-d'))->orderByDesc('id')->get();
@endphp
@if (!blank($frontendAdvertisements))
    <div class="ad-section pt-80">
        <div class="container">
            <div class="ad-slider">
                @foreach ($frontendAdvertisements as $frontendAdvertisement)
                    @php
                        $url = $frontendAdvertisement->url ?? ($frontendAdvertisement->owner_id ? route('hotel.details', $frontendAdvertisement->owner_id) : '#');
                    @endphp
                    <div class="ad-card">
                        <a href="{{ $url }}" class="ad-thumb">
                            <img src="{{ getImage(getFilePath('ads') . '/' . $frontendAdvertisement->image ?? null, getFileSize('ads')) }}"
                                 alt="advertisement image">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('script')
        <script>
            (function($) {
                'use strict';

                $('.ad-slider').slick({
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    arrows: false,
                    Infinity: true,
                    autoplay: true,
                    autoplaySpeed: 2500,
                    speed: 2000,
                    prevArrow: '<button type="button" class="slick-prev"><i class="las la-angle-left"></i></button>',
                    nextArrow: '<button type="button" class="slick-next"><i class="las la-angle-right"></i></button>',
                });
            })(jQuery)
        </script>
    @endpush
@endif
