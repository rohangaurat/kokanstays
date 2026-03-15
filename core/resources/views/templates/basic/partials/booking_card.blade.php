<div class="col-sm-12">
    <div class="booking-card card-three">
        <a href="{{ route('hotel.details', $booking->owner->id) }}" class="booking-card__thumb">
            <img src="{{ getImage(getFilePath('hotelImage') . '/' . $booking->owner->hotelSetting->image ?? null, getFileSize('hotelImage')) }}"
                alt="hotel image">
            @php echo $badge; @endphp
        </a>
        <div class="booking-card__content">
            <div class="booking-card__content-inner">
                <div class="booking-card__wrapper">
                    <div>
                        <h6 class="booking-card__title">
                            <a href="{{ route('hotel.details', $booking->owner->id) }}">
                                {{ __($booking->owner->hotelSetting->name ?? '') }}
                            </a>
                        </h6>
                        <ul class="room-info-list">
                            @if ($bookingId ?? null)
                                <li class="room-info-list__item">
                                    <i class="las la-bed"></i> @lang('Booking Id'): {{ $bookingId }}
                                </li>
                            @endif
                            <li class="room-info-list__item">
                                <i class="las la-map-marked-alt"></i>
                                {{ strLimit(__($booking->owner->hotelSetting->hotel_address ?? ''), 40) }}
                            </li>
                            @if ($totalGuest ?? null)
                                <li class="room-info-list__item">
                                    <i class="las la-user"></i> {{ $totalGuest }} @lang('Guests')
                                </li>
                            @endif
                            <li class="room-info-list__item">
                                <i class="las la-calendar-day"></i>
                                <div class="room-info-list__wrapper">
                                    <p class="room-info-list__text">
                                        @lang('Check In'): {{ showDateTime($booking->check_in, 'd M, Y') }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                        <a href="{{ route($detailsRoute, $booking->id) }}" class="booking-card__link">
                            @lang('Booking Details')
                        </a>
                    </div>
                </div>
                <div class="booking-card__right">
                    @if ($totalAmount ?? null)
                        <p class="price">@lang('Total Amount'): {{ showAmount($totalAmount) }}</p>
                    @endif
                    @if ($bookAgain)
                        <div class="booking-card__right-btn">
                            <div class="d-flex gap-2 flex-wrap justify-content-end align-items-center">
                                @if ($booking->owner->expire_at >= now())
                                    <a href="{{ route('hotel.details', $booking->owner->id) }}"
                                        class="btn btn--base btn--sm">
                                        <i class="las la-shopping-bag"></i> @lang('Book Again')
                                    </a>
                                @endif
                                <button type="button" class="btn btn--danger btn--sm confirmationBtn"
                                    data-action="{{ route('user.booking.delete', $booking->id) }}"
                                    data-question="@lang('Are you sure to delete this booking request?')">
                                    <i class="las la-trash-alt"></i> @lang('Delete')
                                </button>
                            </div>
                        </div>
                    @endif
                    {{-- Custom Fix (KokanStays): correct payable/refundable logic for guest UI --}}
@php
$actualDue = $booking->total_amount - $booking->paid_amount;
@endphp

@if ($actualDue != 0)
    <div class="booking-card__right">
        <p class="price">
            {{ $actualDue < 0 ? __('Refundable') : __('Payable') }}: {{ showAmount(abs($actualDue)) }}
        </p>

        @if ($actualDue > 0)
            <div class="booking-card__right-btn">
                <a href="{{ route('user.deposit.index', $booking->id) }}"
                    class="btn btn--base btn--sm">
                    @lang('Pay Now')
                </a>
            </div>
        @endif
    </div>
@endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
    <style>
        .booking-card.card-three .booking-card__right .price {
            font-size: 16px;
        }

        .booking-card.card-three .booking-card__content-inner {
            align-items: flex-start;
        }

        .booking-card.card-three .room-info-list__text:first-child::after {
            display: none;
        }
    </style>
@endpush
