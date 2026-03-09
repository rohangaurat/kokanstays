@if (!blank($hotels))
    @foreach ($hotels as $hotel)
        <div class="booking-card">
            <div class="booking-card__thumb skeleton less-border hotelDetailsBtn"
                 data-action="{{ route('hotel.details', $hotel->id) }}">
                <img src="{{ getImage(getFilePath('hotelImage') . '/' . $hotel->hotelSetting->image ?? null, getFileSize('hotelImage')) }}"
                     alt="hotel image">
            </div>
            <div class="booking-card__content">
                <div>
                    <h5 class="booking-card__title skeleton hotelDetailsBtn"
                        data-action="{{ route('hotel.details', $hotel->id) }}">
                        {{ __($hotel->hotelSetting->name ?? '') }}
                    </h5>
                    <div class="skeleton">
                        <x-rating-star :rating="$hotel->hotelSetting->avg_rating" :showRating="true" :reviewCount="$hotel->reviews_count" />
                    </div>
                    <p class="booking-card__text skeleton">
                        <span class="booking-card__icon"><i class="las la-map-marker-alt"></i></span>
                        {{ strLimit(__($hotel->hotelSetting->hotel_address ?? ''), 40) }}
                    </p>
                    @php $facilities = $hotel->hotelSetting->facilities()->inRandomOrder()->take(3)->get(); @endphp
                    @if (!blank($facilities))
                        <ul class="booking-card__list">
                            @foreach ($facilities as $facility)
                                <li class="booking-card__item skeleton less-border">
                                    <img src="{{ getImage(getFilePath('facility') . '/' . $facility->image ?? null, getFileSize('facility')) }}"
                                         alt="facility-image">
                                    {{ __($facility->name ?? '') }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="booking-card__right">
                    <span class="text skeleton">@lang('Start Form')</span>
                    <h6 class="price skeleton">{{ showAmount($hotel->minimum_fare) }}</h6>
                    <span class="text skeleton">@lang('for 1 night per room')</span>
                </div>
            </div>
        </div>
    @endforeach

    @if ($hotels->hasPages())
        <div class="skeleton">{{ paginateLinks($hotels) }}</div>
    @endif
@else
    @include('Template::partials.empty_list', ['message' => 'No Hotels Found!'])
@endif

@push('script')
    <script>
        (function($) {
            'use strict';

            $(document).on('click', '.hotelDetailsBtn', function(e) {
                e.preventDefault();
                let form = $('.booking-filter__form');
                form.attr('action', $(this).data('action'));
                form.attr('method', 'GET');
                form.attr('target', '_blank');
                form.submit();
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .booking-card__title {
            cursor: pointer;
        }

        .booking-card__item img {
            width: 15px;
            margin-right: 5px;
        }
    </style>
@endpush
