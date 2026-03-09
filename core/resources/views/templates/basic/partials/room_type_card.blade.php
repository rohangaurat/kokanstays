@if (!blank($roomTypes))
    @foreach ($roomTypes ?? [] as $roomType)
        @php
            $roomTypeImage = getImage(getFilePath('roomTypeImage') . '/' . $roomType->images->first()?->image ?? null);
            $beds = $roomType->bedCount();
            $availableRoomCount = $roomType->rooms()->availableRoomCount($checkIn, $checkout);
            $oldPrice = $roomType->discount_percentage > 0 ? $roomType->fare : 0;
            $price = $roomType->discountedFare;
            $images = $roomType->images;
        @endphp
        <div class="booking-card card-two">
            @if ($images->isNotEmpty())
                <a href="{{ getImage(getFilePath('roomTypeImage') . '/' . $images[0]->image) }}"
                    class="popup-room-img-{{ $roomType->id }} booking-card__thumb skeleton test_popup_image">
                    <img src="{{ getImage(getFilePath('roomTypeImage') . '/' . $images[0]->image) }}" alt="room image">
                </a>
                @foreach ($images->slice(1) as $image)
                    <a href="{{ getImage(getFilePath('roomTypeImage') . '/' . $image->image) }}"
                        class="popup-room-img-{{ $roomType->id }} d-none test_popup_image"></a>
                @endforeach
            @endif
            <div class="booking-card__content">
                <div class="booking-card__content-inner">
                    <div class="booking-card__wrapper">
                        <div>
                            <h6 class="booking-card__title skeleton">
                                {{ __($roomType->name ?? '') }}
                            </h6>
                            <ul class="room-info-list">
                                @if (!blank($beds))
                                    <li class="room-info-list__item skeleton">
                                        <i class="las la-bed"></i>
                                        @foreach ($beds as $bed => $count)
                                            @continue(!$loop->first)
                                            {{ $count . ' ' . __($bed) }}
                                        @endforeach
                                    </li>
                                @endif
                                <li class="room-info-list__item skeleton">
                                    <i class="las la-user"></i>
                                    {{ $roomType->total_adult }} @lang('Adult'),
                                    {{ $roomType->total_child }} @lang('Children')
                                </li>
                                <li class="room-info-list__item skeleton"> <i class="las la-ban"></i>
                                    @lang('Cancellation Fee')
                                    {{ showAmount($roomType->cancellation_fee) }}
                                </li>
                            </ul>
                            <a href="javascript:void(0)" class="booking-card__link skeleton roomDetailBtn"
                                data-image="{{ $roomTypeImage }}" data-beds="{{ json_encode($beds) }}"
                                data-room_type="{{ $roomType }}"
                                data-available_room_count="{{ $availableRoomCount }}"
                                data-amenities="{{ json_encode($roomType->amenities) }}"
                                data-facilities="{{ json_encode($roomType->facilities) }}"
                                data-tax="{{ $hotelTaxPercentage }}">
                                @lang('Room Details')
                            </a>
                        </div>
                        <div class="card-info">
                            <p class="card-info__text skeleton">@lang('Option')</p>
                            <ul class="info-list">
                                <li class="info-list__item skeleton">
                                    @lang('Available Rooms')
                                    {{ $availableRoomCount }}
                                </li>
                                <li class="info-list__item skeleton">
                                    @lang('Fare/Night') {{ showAmount($roomType->fare) }}
                                </li>
                                <li class="info-list__item skeleton">
                                    {{ $roomType->discount_percentage }}% @lang('Discount')
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="booking-card__right">
                        @if ($oldPrice)
                            <div class="skeleton">
                                <span class="old-price">
                                    {{ showAmount($oldPrice) }}
                                </span>
                            </div>
                        @endif
                        <h6 class="price skeleton">
                            {{ showAmount($price) }}
                        </h6>
                        <span class="text skeleton">
                            +{{ $hotelTaxPercentage }}% @lang('taxes')
                        </span>
                        <span class="text skeleton">@lang('for 1 night')</span>
                        <div class="booking-card__right-btn skeleton">
                            <button class="btn btn--base btn--sm addRoom" id="roomType_{{ $roomType->id }}"
                                data-room_type_id="{{ $roomType->id }}"
                                data-room_type_name="{{ __($roomType->name ?? '') }}"
                                data-total_adult="{{ $roomType->total_adult }}"
                                data-total_child="{{ $roomType->total_child }}" data-image="{{ $roomTypeImage }}"
                                data-old_price="{{ $oldPrice }}" data-price="{{ $price }}"
                                data-available_room_count="{{ $availableRoomCount }}">
                                <i class="las la-plus"></i> @lang('Add Room')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    @include('Template::partials.empty_list', ['message' => 'No Room Found!'])
@endif

@push('script')
    <script>
        (function($) {
            'use strict';

            $('[class^="popup-room-img-"]').each(function() {
                let className = $(this).attr('class').split(" ").find(c => c.startsWith("popup-room-img-"));
                if (className) {
                    $('.' + className).magnificPopup({
                        type: 'image',
                        gallery: {
                            enabled: true
                        }
                    });
                }
            });
        })(jQuery);
    </script>
@endpush
