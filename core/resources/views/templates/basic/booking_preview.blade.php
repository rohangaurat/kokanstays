@extends('Template::layouts.frontend')
@php $user = auth()->user(); @endphp
@section('content')
    <div class="payment-section py-80">
        <div class="container">
            <div class="row gy-4">
                <div class="col-xxl-8 col-xl-7">
                    <div class="payment-process">
                        <h5 class="payment-process__title">
                            @lang('Booking Summary')
                        </h5>
                        <p class="payment-process__desc">
                            @lang('Please review all reservation details carefully before proceeding to confirmation.')
                        </p>
                        <div class="payment-details">
                            <div class="payment-details__top">
                                <div class="left">
                                    <h5 class="payment-details__title">
                                        {{ __($hotel->hotelSetting->name ?? '') }}
                                    </h5>
                                    <ul class="text-list">
                                        <li class="text-list__item">
                                            {{ $previewData['totalGuest'] }} @lang('Guest')
                                        </li>
                                        <li class="text-list__item">
                                            {{ array_sum($roomTypes) }} @lang('Room')
                                        </li>
                                    </ul>
                                </div>
                                <div class="right">
                                    <div class="hotel-img">
                                        <img src="{{ getImage(getFilePath('hotelImage') . '/' . $hotel->hotelSetting->image ?? null, getFileSize('hotelImage')) }}"
                                            alt="hotel image" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="booking-box__check">
                                <div class="checking-box">
                                    <span class="checking-box__title">@lang('Check In')</span>
                                    <p class="checking-box__date">
                                        {{ showDateTime($previewData['checkin'], 'D, j M Y') }}
                                    </p>
                                    <p class="checking-box__time">
                                        @lang('From') {{ showDateTime($hotel->hotelSetting->checkin_time, 'h:i A') }}
                                    </p>
                                </div>
                                <p class="checking-box__text">@lang('To')</p>
                                <div class="checking-box">
                                    <span class="checking-box__title">@lang('Check Out')</span>
                                    <p class="checking-box__date">
                                        {{ showDateTime($previewData['checkout'], 'D, j M Y') }}
                                    </p>
                                    <p class="checking-box__time">
                                        @lang('Before') {{ showDateTime($hotel->hotelSetting->checkout_time, 'h:i A') }}
                                    </p>
                                </div>
                            </div>
                            @if (!blank($hotel->roomTypes))
                                <div class="review-container">
                                    @foreach ($hotel->roomTypes as $roomType)
                                        <x-template.room-type-card :roomType="$roomType" />
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="payment-details border border--warning">
                            <h5 class="payment-details__title">
                                @lang('Cancellation Policy')
                            </h5>
                            <p class="payment-details__desc mw-100 m-0">
                                {{ __($hotel->hotelSetting->cancellation_policy ?? '') }}
                            </p>
                        </div>
                        <div class="payment-details">
                            <h5 class="payment-details__title">
                                @lang('Contact Details')
                            </h5>
                            <p class="payment-details__desc mw-100">
                                @lang('Provide accurate contact information to ensure seamless communication regarding your reservation.')
                            </p>
                            <form action="{{ route('user.booking.request.submit') }}" method="POST"
                                class="contact-details-form">
                                @csrf
                                @foreach ($previewData['roomTypes'] as $roomTypeId => $roomCount)
                                    <input type="hidden" name="room_types[{{ $roomTypeId }}]"
                                        value="{{ $roomCount }}">
                                @endforeach
                                <input type="hidden" name="owner_id" value="{{ $hotel->id }}">
                                <input type="hidden" name="checkin" value="{{ $previewData['checkin'] }}">
                                <input type="hidden" name="checkout" value="{{ $previewData['checkout'] }}">
                                <input type="hidden" name="total_adult" value="{{ $previewData['totalAdult'] }}">
                                <input type="hidden" name="total_child" value="{{ $previewData['totalChild'] }}">
                                <div class="form-group">
                                    <label class="form--label">@lang('Guest Name')</label>
                                    <input type="text" class="form--control" name="guest_name" value="{{ $user->fullname }}" required>
                                    <p class="text">
                                        @lang('As mentioned in your passport or governments approved ID card.')
                                    </p>
                                </div>
                                <div class="form-group m-0">
                                    <label class="form--label">@lang('Mobile Number')</label>
                                    <div class="number mw-100">
                                        <select class="form--control select2" name="dial_code" required>
                                            @foreach ($countries as $key => $country)
                                                <option value="{{ $country->dial_code }}" @selected($user->dial_code == $country->dial_code)
                                                    data-img="{{ asset('assets/images/country/' . strtolower($key) . '.svg') }}">
                                                    {{ __($country->dial_code) }} ({{ __($country->country) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="number" name="contact_number" value="{{ $user->mobile }}" class="form--control" required>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-5">
                    <div class="booking-box">
                        <h5 class="booking-box__title">@lang('Booking Summery')</h5>
                        <div class="booking-box__wrapper">
                            <div class="booking-box__check">
                                <div class="checking-box">
                                    <span class="checking-box__title">@lang('Check In')</span>
                                    <p class="checking-box__date">
                                        {{ showDateTime($previewData['checkin'], 'D, j M Y') }}
                                    </p>
                                    <p class="checking-box__time">
                                        @lang('From') {{ showDateTime($hotel->hotelSetting->checkin_time, 'h:i A') }}
                                    </p>
                                </div>
                                <p class="checking-box__text">@lang('To')</p>
                                <div class="checking-box">
                                    <span class="checking-box__title">@lang('Check Out')</span>
                                    <p class="checking-box__date">
                                        {{ showDateTime($previewData['checkout'], 'D, j M Y') }}
                                    </p>
                                    <p class="checking-box__time">
                                        @lang('Before') {{ showDateTime($hotel->hotelSetting->checkout_time, 'h:i A') }}
                                    </p>
                                </div>
                            </div>
                            <ul class="booking-details">
                                @php
                                    $checkIn = Carbon\Carbon::parse($previewData['checkin']);
                                    $checkOut = Carbon\Carbon::parse($previewData['checkout']);
                                    $totalStay = (int) $checkIn->diffInDays($checkOut);
                                    $totalTax = 0;
                                    $totalBill = 0;
                                @endphp
                                @foreach ($hotel->roomTypes as $summaryRoomType)
                                    @php
                                        $discount =
                                            $summaryRoomType->fare * ($summaryRoomType->discount_percentage / 100);
                                        $totalPrice =
                                            ($summaryRoomType->fare - $discount) *
                                            $totalStay *
                                            $roomTypes[$summaryRoomType->id];
                                        $tax = ($totalPrice * $hotel->hotelSetting->tax_percentage) / 100;
                                        $totalTax += $tax;
                                        $totalBill += $totalPrice + $tax;
                                    @endphp
                                    <li class="booking-details__list">
                                        <div>
                                            <span class="booking-details__item">
                                                {{ __($summaryRoomType->name) }}
                                                <p class="booking-details__text">
                                                    @lang('Selected Room'): {{ $roomTypes[$summaryRoomType->id] }}
                                                    |
                                                    @lang('Night Stay'): {{ $totalStay }}
                                                </p>
                                            </span>
                                        </div>
                                        <p class="booking-details__price">{{ showAmount($totalPrice) }}</p>
                                    </li>
                                @endforeach
                                <li class="booking-details__list">
                                    <span class="booking-details__item">@lang('Text & fees')</span>
                                    <p class="booking-details__price">{{ showAmount($totalTax) }}</p>
                                </li>
                                <li class="booking-details__list total">
                                    <span class="booking-details__item">@lang('Total')</span>
                                    <p class="booking-details__price">{{ showAmount($totalBill) }}</p>
                                </li>
                            </ul>
                            <button type="button" class="btn btn--base w-100 btn--lg bookingRequestBtn">
                                @lang('Send Booking Request')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

            $(document).on('click', '.bookingRequestBtn', function(e) {
                e.preventDefault();
                $('.contact-details-form').submit();
            });

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
    </style>
@endpush
