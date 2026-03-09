@extends('Template::layouts.master')
@php
    $hotel = $booking->owner;
    $bookingRequestDetails = $booking->bookingRequestDetails;
    $nightStay = $booking->bookFor();
    $totalGuest = $booking->total_adult + $booking->total_child;

    $totalFare = 0;
    $totalDiscount = 0;
    $totalTax = 0;
    $totalDiscountedFare = 0;
@endphp
@section('content')
    <div class="booking-trip-thumb skeleton">
        <img src="{{ getImage(getFilePath('hotelCoverImage') . '/' . $hotel->hotelSetting->cover_image ?? null, getFileSize('hotelCoverImage')) }}"
            alt="cover image">
        <div class="content">
            <h5 class="thumb-title mb-3">{{ __($hotel->hotelSetting->location->name ?? '') }}</h5>
            <p class="time">
                <span class="booking-card card-three">@php echo $booking->customStatusBadge; @endphp</span>
            </p>
        </div>
    </div>
    <div class="payment-process mt-4">
        <div class="payment-details">
            <div class="payment-details__top">
                <div class="left">
                    <h5 class="payment-details__title skeleton">{{ __($hotel->hotelSetting->name ?? '') }}</h5>
                    <ul class="text-list skeleton">
                        <li class="text-list__item">{{ $totalGuest }} @lang('Guest')</li>
                        <li class="text-list__item">{{ $booking->totalRoom() }} @lang('Room')</li>
                        <li class="text-list__item">{{ $nightStay }} @lang('Night')</li>
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
                @foreach ($bookingRequestDetails as $bookingRequestDetail)
                    @php $roomType = $bookingRequestDetail->roomType; @endphp
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
            <h5 class="title skeleton">@lang('Payment Information')</h5>
            <div class="payment-information">
                @foreach ($bookingRequestDetails as $bookingRequestDetail)
                    @php
                        $roomType = $bookingRequestDetail->roomType;
                        $total = $bookingRequestDetail->unit_fare * $bookingRequestDetail->number_of_rooms * $nightStay;
                        $discountedFare = $total - $bookingRequestDetail->discount;
                        $totalFare += $total;
                        $totalTax += $bookingRequestDetail->tax_charge;
                        $totalDiscount += $bookingRequestDetail->discount;
                        $totalDiscountedFare += $bookingRequestDetail->total_amount;
                    @endphp
                    <span class="payment-information__text {{ $loop->first ? '' : 'mt-3' }}">
                        {{ __($roomType->name ?? '') }}
                    </span>
                    <ul class="text-list skeleton">
                        <li class="text-list__item">
                            <i class="las la-home"></i> {{ $bookingRequestDetail->number_of_rooms }} @lang('Room')
                        </li>
                        <li class="text-list__item">
                            <i class="las la-coins"></i> @lang('Fare Per Room')
                            {{ showAmount($bookingRequestDetail->unit_fare) }}
                        </li>
                    </ul>
                    <ul class="payment-list">
                        <li class="payment-list__item">
                            <span class="payment-list__text">@lang('Total')</span>
                            <span class="payment-list__price">{{ showAmount($total) }}</span>
                        </li>
                        @if ($bookingRequestDetail->discount > 0)
                            <li class="payment-list__item">
                                <span class="payment-list__text">@lang('Discount')</span>
                                <span class="payment-list__price">
                                    - {{ showAmount($bookingRequestDetail->discount) }}
                                </span>
                            </li>
                            <li class="payment-list__item">
                                <span class="payment-list__text">@lang('Discounted Fee')</span>
                                <span class="payment-list__price">{{ showAmount($discountedFare) }}</span>
                            </li>
                        @endif
                        <li class="payment-list__item">
                            <span class="payment-list__text">
                                {{ __($hotel->hotelSetting->tax_name ?? '') }} @lang('Charge')
                                ({{ $hotel->hotelSetting->tax_percentage }}%)
                            </span>
                            <span class="payment-list__price">+ {{ showAmount($bookingRequestDetail->tax_charge) }}</span>
                        </li>
                    </ul>
                    <ul class="payment-list">
                        <li class="payment-list__item item-two">
                            <span class="payment-list__text">@lang('Subtotal')</span>
                            <span class="payment-list__price">
                                = {{ showAmount($bookingRequestDetail->total_amount) }}
                            </span>
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
                            @lang('Total') {{ __($hotel->hotelSetting->tax_name ?? '') }} @lang('Charge')
                        </span>
                        <span class="payment-list__price">+ {{ showAmount($totalTax) }}</span>
                    </li>
                </ul>
                <div class="payment-information__bottom">
                    <span class="total-payment">@lang('Total Payable Amount')</span>
                    <span class="total-payment">= {{ showAmount($totalDiscountedFare) }}</span>
                </div>
            </div>
        </div>

        @include('Template::partials.hotel_policy')
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

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
    </style>
@endpush
