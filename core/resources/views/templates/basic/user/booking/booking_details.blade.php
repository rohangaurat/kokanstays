@extends('Template::layouts.master')
@php
    $hotel = $booking->owner;
    $checkIn = Carbon\Carbon::parse($booking->check_in);
    $checkOut = Carbon\Carbon::parse($booking->check_out);
    $totalStay = (int) $checkIn->diffInDays($checkOut);
    $totalGuest = $booking->total_adult + $booking->total_child;
    $totalRooms = $booking->bookedRooms->count();

    $totalFare = 0;
    $totalDiscount = 0;
    $totalDiscountedFare = 0;
    $totalTax = 0;
    $totalBill = 0;

    $canceledFare = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('fare');
    $canceledTaxCharge = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('tax_charge');
    $due = $booking->total_amount - $booking->paid_amount;
@endphp
@section('content')
    <div class="booking-trip-thumb skeleton">
        <img src="{{ getImage(getFilePath('hotelCoverImage') . '/' . $hotel->hotelSetting->cover_image ?? null, getFileSize('hotelCoverImage')) }}"
            alt="cover image">
        <div class="content">
            <h5 class="thumb-title">{{ __($hotel->hotelSetting->location->name ?? '') }}</h5>
            <p class="time mb-2">
                @lang('Booking ID'): <span class="fw-bold">{{ $booking->booking_number }}</span>
            </p>
            <p class="time">
                <span class="booking-card card-three">@php echo $booking->statusCustomBadge; @endphp</span>
            </p>
        </div>
    </div>
    <div class="payment-process mt-4">
        <div class="payment-details">
            <div class="payment-details__top">
                <div class="left">
                    <h5 class="payment-details__title skeleton">{{ __($hotel->hotelSetting->name ?? '') }}</h5>
                    <ul class="text-list skeleton">
                        <li class="text-list__item">
                            <i class="las la-user-tie"></i> {{ $totalGuest }} @lang('Guests')
                        </li>
                        <li class="text-list__item">
                            <i class="las la-home"></i> {{ $totalRooms }} @lang('Room')
                        </li>
                        <li class="text-list__item">
                            <i class="las la-sun"></i> {{ $totalStay }} @lang('Night')
                        </li>
                    </ul>
                </div>
                <div class="right">
                    <div class="hotel-img skeleton">
                        <img src="{{ getImage(getFilePath('hotelImage') . '/' . $hotel->hotelSetting->image ?? null, getFileSize('hotelImage')) }}"
                            alt="hotel image" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="booking-box__check skeleton">
                <div class="checking-box">
                    <span class="checking-box__title">@lang('Check In')</span>
                    <p class="checking-box__date">{{ showDateTime($booking->check_in, 'D, j M Y') }}</p>
                    <p class="checking-box__time">
                        @lang('From') {{ showDateTime($hotel->hotelSetting->checkin_time, 'h:i A') }}
                    </p>
                </div>
                <p class="checking-box__text">@lang('To')</p>
                <div class="checking-box">
                    <span class="checking-box__title">@lang('Check Out')</span>
                    <p class="checking-box__date">{{ showDateTime($booking->check_out, 'D, j M Y') }}</p>
                    <p class="checking-box__time">
                        @lang('Before') {{ showDateTime($hotel->hotelSetting->checkout_time, 'h:i A') }}
                    </p>
                </div>
            </div>
            <div class="review-container">
                @foreach ($roomTypes as $roomType)
                    <x-template.room-type-card :roomType="$roomType" />
                @endforeach
            </div>
        </div>
    </div>
    <div class="hotel-details__tab mt-4">
        <div class="hotel-details__item">
            <h5 class="title skeleton">@lang('Contact Information')</h5>
            <div class="guest-info-wrapper">
                <p class="name">
                    <span class="icon"><i class="las la-user"></i></span>
                    {{ $booking->contact_info->name ?? __('N/A') }}
                </p>
                <p class="phone">
                    <span class="icon"><i class="las la-phone"></i></span>
                    {{ $booking->contact_info->phone ?? __('N/A') }}
                </p>
            </div>
        </div>
        <div class="hotel-details__item widget_component-wrapper">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="title skeleton">@lang('Payment Information')</h5>
                <div class="d-flex gap-1 align-items-center justify-content-end flex-wrap">
                    @if ($booking->due_amount != 0)
                        <a href="{{ route('user.deposit.index', $booking->id) }}" class="btn btn--base btn--sm"
                            target="_blank">
                            <i class="las la-file-invoice-dollar"></i> @lang('Pay Now')
                        </a>
                    @endif
                    <a href="{{ route('user.booking.invoice', $booking->id) }}" class="btn btn--sm btn-outline--secondary"
                        target="_blank">
                        <i class="las la-print"></i> @lang('Print Invoice')
                    </a>
                </div>
            </div>
            <div class="payment-information">
                @foreach ($roomTypes as $roomType)
                    @php
                        $roomCount = $roomType->bookedRooms->where('booking_id', $booking->id)->count();
                        $bookedRoom = $roomType->bookedRooms()->where('booking_id', $booking->id)->first();
                        $total = $bookedRoom->fare * $roomCount;
                        $discount = $bookedRoom->discount * $roomCount;
                        $discountedFare = $total - $discount;
                        $taxCharge = $bookedRoom->tax_charge * $roomCount;
                        $subTotal = $discountedFare + $taxCharge;

                        $totalFare += $total;
                        $totalDiscount += $discount;
                        $totalDiscountedFare += $discountedFare;
                        $totalTax += $taxCharge;
                        $totalBill += $subTotal;
                    @endphp
                    <span class="payment-information__text {{ $loop->first ? '' : 'mt-3' }}">
                        {{ __($roomType->name ?? '') }}
                    </span>
                    <ul class="text-list skeleton">
                        <li class="text-list__item">
                            <i class="las la-home"></i> {{ $roomCount }} @lang('Room')
                        </li>
                        <li class="text-list__item">
                            <i class="las la-coins"></i>
                            @lang('Fare Per Room') {{ showAmount($bookedRoom->fare) }}
                        </li>
                    </ul>
                    <ul class="payment-list">
                        <li class="payment-list__item">
                            <span class="payment-list__text">@lang('Total')</span>
                            <span class="payment-list__price">{{ showAmount($total) }}</span>
                        </li>
                        @if ($bookedRoom->discount > 0)
                            <li class="payment-list__item">
                                <span class="payment-list__text">@lang('Discount')</span>
                                <span class="payment-list__price">- {{ showAmount($discount) }}</span>
                            </li>
                            <li class="payment-list__item">
                                <span class="payment-list__text">@lang('Discounted Fare')</span>
                                <span class="payment-list__price">{{ showAmount($discountedFare) }}</span>
                            </li>
                        @endif
                        <li class="payment-list__item">
                            <span class="payment-list__text">
                                {{ __($hotel->hotelSetting->tax_name) }} @lang('Charge')
                                ({{ $hotel->hotelSetting->tax_percentage }}%)
                            </span>
                            <span class="payment-list__price">+ {{ showAmount($taxCharge) }}</span>
                        </li>
                    </ul>
                    <ul class="payment-list">
                        <li class="payment-list__item item-two">
                            <span class="payment-list__text">@lang('Subtotal')</span>
                            <span class="payment-list__price">= {{ showAmount($subTotal) }}</span>
                        </li>
                    </ul>
                @endforeach
                <ul class="payment-list">
                    <li class="payment-list__item">
                        <span class="payment-list__text">@lang('Total Fare')</span>
                        <span class="payment-list__price">{{ showAmount($totalFare) }}</span>
                    </li>
                    @if ($totalDiscount > 0)
                        <li class="payment-list__item">
                            <span class="payment-list__text">@lang('Total Discount')</span>
                            <span class="payment-list__price">- {{ showAmount($totalDiscount) }}</span>
                        </li>
                        <li class="payment-list__item">
                            <span class="payment-list__text">@lang('Total Discounted Fare')</span>
                            <span class="payment-list__price">{{ showAmount($totalDiscountedFare) }}</span>
                        </li>
                    @endif
                    <li class="payment-list__item">
                        <span class="payment-list__text">
                            @lang('Total') {{ __($hotel->hotelSetting->tax_name) }} @lang('Charge')
                        </span>
                        <span class="payment-list__price">+ {{ showAmount($totalTax) }}</span>
                    </li>
                </ul>
                <ul class="payment-list">
                    <li class="payment-list__item item-two">
                        <span class="payment-list__text">@lang('Final Amount')</span>
                        <span class="payment-list__price">= {{ showAmount($totalBill) }}</span>
                    </li>
                </ul>
                @if ($booking->status == Status::BOOKING_CANCELED)
                    <ul class="payment-list">
                        <li class="payment-list__item">
                            <span class="payment-list__text">@lang('Canceled Fare')</span>
                            <span class="payment-list__price">{{ showAmount($canceledFare ?? 0) }}</span>
                        </li>
                        <li class="payment-list__item">
                            <span class="payment-list__text">
                                @lang('Canceled ') {{ __($hotel->hotelSetting->tax_name ?? '') }} @lang('Charge')
                            </span>
                            <span class="payment-list__price">{{ showAmount($canceledTaxCharge ?? 0) }}</span>
                        </li>
                        <li class="payment-list__item">
                            <span class="payment-list__text">@lang('Canceled Fee')</span>
                            <span class="payment-list__price">{{ showAmount($booking->cancellation_fee ?? 0) }}</span>
                        </li>
                        <li class="payment-list__item">
                            <span class="payment-list__text">@lang('Canceled Discount')</span>
                            <span class="payment-list__price">- {{ showAmount($totalDiscount) }}</span>
                        </li>
                        @if ($booking->service_cost > 0)
                            <li class="payment-list__item">
                                <span class="payment-list__text">@lang('Extra Service Charge')</span>
                                <span class="payment-list__price">+ {{ showAmount($booking->service_cost) }}</span>
                            </li>
                        @endif
                        @if ($booking->extraCharge() > 0)
                            <li class="payment-list__item">
                                <span class="payment-list__text">@lang('Extra Charge')</span>
                                <span class="payment-list__price">+ {{ showAmount($booking->extraCharge()) }}</span>
                            </li>
                        @endif
                    </ul>
                    <ul class="payment-list">
                        <li class="payment-list__item item-two">
                            <span class="payment-list__text">@lang('Remaining Cancellation Fee')</span>
                            <span class="payment-list__price">= {{ showAmount($booking->total_amount) }}</span>
                        </li>
                    </ul>
                @else
                    @if ($booking->service_cost > 0 || $booking->extraCharge() > 0)
                        <ul class="payment-list">
                            @if ($booking->service_cost > 0)
                                <li class="payment-list__item">
                                    <span class="payment-list__text">@lang('Extra Service Charge')</span>
                                    <span class="payment-list__price">+ {{ showAmount($booking->service_cost) }}</span>
                                </li>
                            @endif
                            @if ($booking->extraCharge() > 0)
                                <li class="payment-list__item">
                                    <span class="payment-list__text">@lang('Extra Charge')</span>
                                    <span class="payment-list__price">+ {{ showAmount($booking->extraCharge()) }}</span>
                                </li>
                            @endif
                        </ul>
                    @endif
                @endif
                @if ($booking->paid_amount > 0)
                    <ul class="payment-list">
                        <li class="payment-list__item">
                            <span class="payment-list__text">@lang('Paid Amount')</span>
                            <span class="payment-list__price">{{ showAmount($booking->paid_amount) }}</span>
                        </li>
                    </ul>
                @endif
                @php $due = $booking->due_amount; @endphp
                <div class="payment-information__bottom">
                    <span class="total-payment">
                        {{ $due < 0 ? __('Receivable') : __('Payable') }}
                    </span>
                    <span class="total-payment">= {{ showAmount(abs($due)) }}</span>
                </div>
            </div>
        </div>

        @include('Template::partials.hotel_policy')

        @if (auth()->check() && !$authUserReview && $booking->status == Status::BOOKING_CHECKOUT)
            <div class="hotel-details__item widget_component-wrapper" id="scrollHeadingFive">
                <div class="review-form">
                    <div class="review-form__top">
                        <h5 class="review-form__title">@lang('Write Your Opinion')</h5>
                        <ul class="rating-list rating-three rating-two mt-4" id="ratingArea">
                            @for ($i = 1; $i <= 5; $i++)
                                <li class="rating-list__item disabled userRatingTrigger"
                                    data-rating="{{ $i }}" role="button">
                                    <span class="icon">
                                        <i class="las la-star h2 pt-2"></i>
                                    </span>
                                </li>
                            @endfor
                        </ul>
                        <form action="{{ route('user.review.submit', $hotel->id) }}" method="post">
                            @csrf
                            <input type="hidden" name="rating" id="rating" value="0">
                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                            <div class="form-group">
                                <label class="form--label">@lang('Title')</label>
                                <input type="text" class="form--control" name="title" value="{{ old('title') }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Comment')</label>
                                <textarea name="comment" class="form--control" required>{{ old('comment') }}</textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn--base btn--lg">
                                    <i class="las la-reply"></i> @lang('Submit')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

            @if (auth()->check() && !$authUserReview && $booking->status == Status::BOOKING_CHECKOUT)
                $('.userRatingTrigger').on('click', function() {
                    var rating = $(this).data('rating');
                    $('#rating').val(rating);
                    $('.userRatingTrigger').addClass('disabled');
                    $('#ratingArea').removeClass('success-deep success warning danger');
                    for (var i = 1; i <= rating; i++) {
                        $(`.userRatingTrigger[data-rating="${i}"]`).removeClass('disabled');
                    }
                });
            @endif

            setTimeout(function() {
                $('.skeleton').removeClass('skeleton');
            }, 1000);
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .review-card__info {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .guest-info-wrapper {
            align-items: center;
            gap: 10px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .guest-info-wrapper .icon {
            color: hsl(var(--heading-color));
            font-size: 20px;
        }

        .guest-info-wrapper p {
            display: flex;
            align-items: center;
            gap: 7px;
            color: hsl(var(--heading-color));
            font-size: 14px;
            border: 1px solid hsl(var(--black)/.2);
            padding: 6px 20px;
            width: 100%;
            border-radius: 4px;
        }

        .payment-information__text {
            font-size: 16px;
        }

        .booking-card {
            background-color: unset !important;
            padding: 0 !important;
            margin-bottom: 0 !important;
        }

        .booking-card.card-three .booking-card__badge {
            position: unset;
        }

        .rating-list.rating-two .rating-list__item {
            width: 32px;
            height: 32px;
            background: hsl(var(--base));
            display: flex;
            justify-content: center;
            align-items: center;
            color: hsl(var(--white));
            cursor: pointer;
        }

        .rating-list.rating-two {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .rating-list.rating-two .rating-list__item.disabled {
            background: hsl(var(--black)/0.2);
        }
    </style>
@endpush
