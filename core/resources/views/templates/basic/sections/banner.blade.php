@php
    $frontendBannerContent = getContent('banner.content', true);
    $cities = App\Models\City::active()
        ->with([
            'country' => function ($q) {
                $q->active();
            },
        ])
        ->get();
@endphp
<section class="banner-section bg-img"
         data-background-image="{{ frontendImage('banner', $frontendBannerContent?->data_values?->image ?? null, '1950x550') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="banner-content">
                    <h2 class="banner-content__title">
                        {{ __($frontendBannerContent?->data_values?->heading ?? '') }}
                    </h2>
                    <p class="banner-content__desc">
                        {{ __($frontendBannerContent?->data_values?->subheading ?? '') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="booking-filter-section">
    <div class="container">
        @include('Template::partials.booking_filter')
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.booking-filter__form').attr('action', "{{ route('hotel.index') }}");
            $('.booking-filter__form').attr('method', "GET");
        })(jQuery);
    </script>
@endpush
