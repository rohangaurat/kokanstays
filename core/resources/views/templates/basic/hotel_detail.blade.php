@extends('Template::layouts.frontend')
@php $hotelTaxPercentage = $hotel->hotelSetting->tax_percentage; @endphp
@section('content')
    <div class="filter-section">
        <div class="container">
            @include('Template::partials.booking_filter')
        </div>
    </div>
    <div class="hotel-details">
        <div class="container position-relative">
            <div class="products-overlay-container" id="products-overlay">
                <div id="overlay">
                    <div class="cv-spinner">
                        <span class="spinner"></span>
                    </div>
                </div>
                <div class="overlay-2" id="overlay2"></div>
            </div>
            <div class="hotel-details-top">
                <div class="row gy-4 align-items-center">
                    <div class="col-lg-7 pe-lg-4">
                        <div class="hotel-details-slider">
                            <div class="hotel-details__thumb">
                                @foreach ($hotel->coverPhotos ?? [] as $coverPhoto)
                                    <a href="{{ getImage(getFilePath('coverPhoto') . '/' . $coverPhoto->cover_photo ?? '') }}"
                                        class="hotel-details__image popup-img skeleton">
                                        <img src="{{ getImage(getFilePath('coverPhoto') . '/' . $coverPhoto->cover_photo ?? '') }}"
                                            alt="cover image">
                                    </a>
                                @endforeach
                            </div>
                            <div class="hotel-details-gallery">
                                @for ($i = 0; $i < 3; $i++)
                                    @if (!isset($hotel->coverPhotos[$i]))
                                        @continue;
                                    @endif
                                    <a href="{{ getImage(getFilePath('coverPhoto') . '/' . $hotel->coverPhotos[$i]->cover_photo ?? '') }}"
                                        class="popup-img skeleton">
                                        <img src="{{ getImage(getFilePath('coverPhoto') . '/' . $hotel->coverPhotos[$i]->cover_photo ?? '') }}"
                                            alt="cover image">
                                        @if ($i == 2 && isset($hotel->coverPhotos[$i + 1]))
                                            <span class="text-overlay">+{{ count($hotel->coverPhotos) - 3 }}</span>
                                        @endif
                                    </a>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="hotel-details__header-right">
                            <h4 class="booking-card__title skeleton">
                                {{ __($hotel->hotelSetting->name ?? '') }}
                            </h4>
                            <div class="skeleton">
                                <x-rating-star :rating="$hotel->hotelSetting->avg_rating" :showRating="true" :reviewCount="$hotel->reviews_count" />
                            </div>
                            <p class="booking-card__text skeleton">
                                <span class="booking-card__icon"><i class="las la-map-marker-alt"></i></span>
                                {{ __($hotel->hotelSetting->hotel_address ?? '') }}
                            </p>
                            <p class="booking-card__desc skeleton mb-0">
                                {{ strLimit(__($hotel->hotelSetting->description ?? ''), 260) }}
                            </p>
                            @php $facilities = $hotel->hotelSetting->facilities()->take(7)->get(); @endphp
                            @if (!blank($facilities))
                                <h6 class="booking-card__list-title skeleton mt-3">
                                    @lang('Facilities')
                                </h6>
                                <ul class="booking-card__list mt-0">
                                    @foreach ($facilities as $key => $facility)
                                        <li class="booking-card__item skeleton less-border">
                                            <img src="{{ getImage(getFilePath('facility') . '/' . $facility->image ?? null, getFileSize('facility')) }}"
                                                alt="">
                                            {{ __($facility->name ?? '') }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="hotel-details__wrapper">
                <div class="row gy-5">
                    <div class="col-xl-8">
                        <div class="hotel-details__tab" id="search-section">
                            <nav class="navbar hotel-details__navbar">
                                <ul class="nav nav-pills custom--tab hotel-details-tab">
                                    <li class="nav-item skeleton">
                                        <a class="nav-link content-block-link active" href="#scrollHeadingOne">
                                            @lang('Rooms')
                                        </a>
                                    </li>
                                    @if ($hotel->hotelSetting->description)
                                        <li class="nav-item skeleton">
                                            <a class="nav-link content-block-link" href="#scrollDescription">
                                                @lang('Description')
                                            </a>
                                        </li>
                                    @endif
                                    <li class="nav-item skeleton">
                                        <a class="nav-link content-block-link" href="#scrollHeadingTwo">
                                            @lang('Where you\'ll be')
                                        </a>
                                    </li>
                                    @if (!blank($facilities))
                                        <li class="nav-item skeleton">
                                            <a class="nav-link content-block-link" href="#scrollHeadingThree">
                                                @lang('Facilities')
                                            </a>
                                        </li>
                                    @endif
                                    <li class="nav-item skeleton">
                                        <a class="nav-link content-block-link" href="#scrollHeadingFour">
                                            @lang('Policy')
                                        </a>
                                    </li>
                                    @if (!blank($reviews))
                                        <li class="nav-item skeleton">
                                            <a class="nav-link content-block-link" href="#scrollHeadingFive">
                                                @lang('Review')
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                            <div class="hotel-details__room widget_component-wrapper" id="scrollHeadingOne">
                                <div class="roomTypeWrapper">
                                    @include('Template::partials.room_type_card')
                                </div>
                                <div class="d-xl-none d-block mt-4">
                                    <div class="hotel-details__summery">
                                        <h5 class="hotel-details__summery-title skeleton">@lang('Pricing Summery')</h5>
                                        <div class="summery-details">
                                            <div class="room-summery">
                                                <div class="no-room">
                                                    <div class="no-room__icon skeleton">
                                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0"
                                                            viewBox="0 0 512 512.001"
                                                            style="enable-background:new 0 0 512 512" xml:space="preserve"
                                                            class="">
                                                            <g>
                                                                <path
                                                                    d="M348.945 221.64v-96.894c0-2.773-1.28-5.336-3.093-7.363L237.219 3.309C235.19 1.176 232.309 0 229.429 0H57.196C25.398 0 0 25.93 0 57.73v325.684c0 31.8 25.398 57.305 57.195 57.305h135.953C218.863 483.402 265.605 512 318.852 512c80.886 0 146.941-65.734 146.941-146.727.11-70.75-50.688-129.867-116.848-143.632ZM240.102 37.458l72.882 76.723h-47.273c-14.086 0-25.61-11.63-25.61-25.715ZM57.195 419.375c-19.953 0-35.851-16.008-35.851-35.96V57.73c0-20.062 15.898-36.386 35.851-36.386h161.563v67.12c0 25.93 21.023 47.06 46.953 47.06h61.89v83.34c-3.199-.106-5.761-.427-8.535-.427-37.242 0-71.496 14.301-97.32 36.711H86.223c-5.871 0-10.672 4.801-10.672 10.668 0 5.872 4.8 10.672 10.672 10.672h115.675c-7.578 10.672-13.875 21.344-18.78 33.082H86.222c-5.871 0-10.672 4.801-10.672 10.672 0 5.867 4.8 10.672 10.672 10.672h89.957c-2.668 10.672-4.055 22.516-4.055 34.36 0 19.206 3.734 38.203 10.457 54.21H57.195Zm261.766 71.39c-69.149 0-125.387-56.238-125.387-125.386 0-69.149 56.13-125.387 125.387-125.387 69.254 0 125.383 56.238 125.383 125.387 0 69.148-56.235 125.387-125.383 125.387Zm0 0"
                                                                    style="stroke:none;fill-rule:nonzero;fill-opacity:1;"
                                                                    fill="currentColor" data-original="#000000"
                                                                    class="">
                                                                </path>
                                                                <path
                                                                    d="M86.223 223.027H194.32c5.871 0 10.672-4.804 10.672-10.672 0-5.87-4.8-10.671-10.672-10.671H86.223c-5.871 0-10.672 4.8-10.672 10.671 0 5.868 4.8 10.672 10.672 10.672ZM362.39 355.348h-32.652V322.16c0-5.867-4.804-10.672-10.672-10.672-5.87 0-10.671 4.805-10.671 10.672v33.188h-33.188c-5.871 0-10.672 4.804-10.672 10.672 0 5.87 4.8 10.671 10.672 10.671h33.188v32.653c0 5.87 4.8 10.672 10.671 10.672 5.868 0 10.672-4.801 10.672-10.672V376.69h32.653c5.87 0 10.671-4.8 10.671-10.671 0-5.868-4.8-10.672-10.671-10.672Zm0 0"
                                                                    style="stroke:none;fill-rule:nonzero;fill-opacity:1;"
                                                                    fill="currentColor" data-original="#000000"
                                                                    class="">
                                                                </path>
                                                            </g>
                                                        </svg>
                                                    </div>
                                                    <span class="title skeleton">@lang('Add Rooms to Continue')</span>
                                                </div>
                                            </div>
                                            <div class="total-summery">
                                                <span class="total-summery__text skeleton">@lang('Total')</span>
                                                <div class="d-flex flex-column align-items-end">
                                                    <h6 class="total-summery__price totalPrice skeleton text-end">
                                                        {{ showAmount(0) }}</h6>
                                                    <span class="total-summery__tax skeleton">
                                                        <span class="taxes_fees">{{ showAmount(0) }}</span>
                                                        @lang('Taxes & Fees')
                                                    </span>
                                                    <span class="total-summery__tax skeleton">
                                                        @lang('For') <span class="total_stays">0</span>
                                                        @lang('Nights')
                                                    </span>
                                                </div>
                                            </div>
                                            <form action="{{ route('hotel.booking.preview.request') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="room_type_ids">
                                                <input type="hidden" name="owner_id" value="{{ $hotel->id }}">
                                                <input type="hidden" name="checkin">
                                                <input type="hidden" name="checkout">
                                                <input type="hidden" name="total_adult">
                                                <input type="hidden" name="total_child">
                                                <button
                                                    class="btn btn--base w-100 btn--lg bookingContinueBtn skeleton less-border"
                                                    type="button" @disabled(true)>
                                                    @lang('Continue')
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hotel-details__item widget_component-wrapper" id="scrollDescription">
                                <h5 class="title skeleton">@lang('Description')</h5>
                                <div class="policy-item">
                                    <div class="policy-item__content">
                                        <p class="policy-item__text skeleton">
                                            {{ __($hotel->hotelSetting->description ?? '') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="hotel-details__item widget_component-wrapper" id="scrollHeadingTwo">
                                <h5 class="title skeleton">@lang('Where you\'ll be')</h5>
                                <div class="map skeleton">
                                    <iframe
    width="100%"
    height="450"
    style="border:0"
    loading="lazy"
    allowfullscreen
    src="https://www.google.com/maps?q={{ urlencode($hotel->hotelSetting->name) }},{{ $hotel->hotelSetting->latitude }},{{ $hotel->hotelSetting->longitude }}&z=16&output=embed">
</iframe>
                                </div>
                            </div>

                            @include('Template::partials.hotel_facility')
                            @include('Template::partials.hotel_policy')
                            @include('Template::partials.review')
                        </div>
                    </div>
                    <div class="col-xl-4 d-xl-block d-none">
                        <div class="hotel-details__summery">
                            <h5 class="hotel-details__summery-title skeleton">@lang('Pricing Summery')</h5>
                            <div class="summery-details">
                                <div class="room-summery">
                                    <div class="no-room">
                                        <div class="no-room__icon skeleton">
                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0"
                                                viewBox="0 0 512 512.001" style="enable-background:new 0 0 512 512"
                                                xml:space="preserve" class="">
                                                <g>
                                                    <path
                                                        d="M348.945 221.64v-96.894c0-2.773-1.28-5.336-3.093-7.363L237.219 3.309C235.19 1.176 232.309 0 229.429 0H57.196C25.398 0 0 25.93 0 57.73v325.684c0 31.8 25.398 57.305 57.195 57.305h135.953C218.863 483.402 265.605 512 318.852 512c80.886 0 146.941-65.734 146.941-146.727.11-70.75-50.688-129.867-116.848-143.632ZM240.102 37.458l72.882 76.723h-47.273c-14.086 0-25.61-11.63-25.61-25.715ZM57.195 419.375c-19.953 0-35.851-16.008-35.851-35.96V57.73c0-20.062 15.898-36.386 35.851-36.386h161.563v67.12c0 25.93 21.023 47.06 46.953 47.06h61.89v83.34c-3.199-.106-5.761-.427-8.535-.427-37.242 0-71.496 14.301-97.32 36.711H86.223c-5.871 0-10.672 4.801-10.672 10.668 0 5.872 4.8 10.672 10.672 10.672h115.675c-7.578 10.672-13.875 21.344-18.78 33.082H86.222c-5.871 0-10.672 4.801-10.672 10.672 0 5.867 4.8 10.672 10.672 10.672h89.957c-2.668 10.672-4.055 22.516-4.055 34.36 0 19.206 3.734 38.203 10.457 54.21H57.195Zm261.766 71.39c-69.149 0-125.387-56.238-125.387-125.386 0-69.149 56.13-125.387 125.387-125.387 69.254 0 125.383 56.238 125.383 125.387 0 69.148-56.235 125.387-125.383 125.387Zm0 0"
                                                        style="stroke:none;fill-rule:nonzero;fill-opacity:1;"
                                                        fill="currentColor" data-original="#000000" class="">
                                                    </path>
                                                    <path
                                                        d="M86.223 223.027H194.32c5.871 0 10.672-4.804 10.672-10.672 0-5.87-4.8-10.671-10.672-10.671H86.223c-5.871 0-10.672 4.8-10.672 10.671 0 5.868 4.8 10.672 10.672 10.672ZM362.39 355.348h-32.652V322.16c0-5.867-4.804-10.672-10.672-10.672-5.87 0-10.671 4.805-10.671 10.672v33.188h-33.188c-5.871 0-10.672 4.804-10.672 10.672 0 5.87 4.8 10.671 10.672 10.671h33.188v32.653c0 5.87 4.8 10.672 10.671 10.672 5.868 0 10.672-4.801 10.672-10.672V376.69h32.653c5.87 0 10.671-4.8 10.671-10.671 0-5.868-4.8-10.672-10.671-10.672Zm0 0"
                                                        style="stroke:none;fill-rule:nonzero;fill-opacity:1;"
                                                        fill="currentColor" data-original="#000000" class="">
                                                    </path>
                                                </g>
                                            </svg>
                                        </div>
                                        <span class="title skeleton">@lang('Add Rooms to Continue')</span>
                                    </div>
                                </div>
                                <div class="total-summery">
                                    <span class="total-summery__text skeleton">@lang('Total')</span>
                                    <div class="d-flex flex-column align-items-end">
                                        <h6 class="total-summery__price totalPrice skeleton text-end">{{ showAmount(0) }}
                                        </h6>
                                        <span class="total-summery__tax skeleton">
                                            <span class="taxes_fees">{{ showAmount(0) }}</span> @lang('Taxes & Fees')
                                        </span>
                                        <span class="total-summery__tax skeleton">
                                            @lang('For') <span class="total_stays">0</span> @lang('Nights')
                                        </span>
                                    </div>
                                </div>
                                <form action="{{ route('hotel.booking.preview.request') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="room_type_ids">
                                    <input type="hidden" name="owner_id" value="{{ $hotel->id }}">
                                    <input type="hidden" name="checkin">
                                    <input type="hidden" name="checkout">
                                    <input type="hidden" name="total_adult">
                                    <input type="hidden" name="total_child">
                                    <button class="btn btn--base w-100 btn--lg bookingContinueBtn skeleton less-border"
                                        type="button" @disabled(true)>
                                        @lang('Continue')
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    <div class="modal fade custom--modal" id="roomDetailModel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">@lang('Room Type Details')</h6>
                    <button type="button" class="close-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="hotel-room-details">
                        <div class="hotel-room-details__item d-flex justify-content-between gap-3 align-items-center pt-3">
                            <div>
                                <p class="hotel-room-details__title"></p>
                                <span class="hotel-room-details__text bed"></span><br>
                                <span class="hotel-room-details__text capacity"></span>
                            </div>
                            <div class="hotel-room-details__right">
                                <div class="hotel-room-details__thumb">
                                    <img src="" alt="room image" class="fit-image roomTypeImage">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="info-wrapper">
                        <div class="payment-information w-100">
                            <h6 class="payment-information__title mb-3">@lang('Relevant Information')</h6>
                            <ul class="payment-list">
                                <li class="payment-list__item">
                                    <span class="payment-list__text">
                                        @lang('Cancellation Fee')
                                    </span>
                                    <span class="payment-list__price cancellationFee"></span>
                                </li>
                                <li class="payment-list__item">
                                    <span class="payment-list__text">
                                        @lang('Available Rooms')
                                    </span>
                                    <span class="payment-list__price availableRooms"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="info-wrapper border-top-0 mt-0">
                        <div class="payment-information w-100">
                            <h6 class="payment-information__title mb-3">@lang('Payment Information')</h6>
                            <ul class="payment-list">
                                <li class="payment-list__item">
                                    <span class="payment-list__text">
                                        @lang('Fare/Night')
                                    </span>
                                    <span class="payment-list__price farePerNight"></span>
                                </li>
                                <li class="payment-list__item">
                                    <span class="payment-list__text">
                                        @lang('Discount')
                                    </span>
                                    <span class="payment-list__price discountedPercentage"></span>
                                </li>
                                <li class="payment-list__item">
                                    <span class="payment-list__text">
                                        @lang('Discounted Fare/Night')
                                    </span>
                                    <span class="payment-list__price discountedFarePerNight"></span>
                                </li>
                                <li class="payment-list__item">
                                    <span class="payment-list__text">
                                        @lang('Tax & Fees')
                                    </span>
                                    <span class="payment-list__price taxesFees"></span>
                                </li>
                                <li class="payment-list__item item-two">
                                    <span class="payment-list__text text--base-two fw-600">
                                        @lang('Total Fare/Night')
                                    </span>
                                    <span class="payment-list__price totalFarePerNight"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start info-wrapper border-top-0 mt-0">
                        <div class="payment-information">
                            <h6 class="payment-information__title mb-3">@lang('Amenities')</h6>
                            <ul class="payment-list amenities"></ul>
                        </div>
                        <div class="payment-information">
                            <h6 class="payment-information__title mb-3">@lang('Facilities')</h6>
                            <ul class="payment-list facilities"></ul>
                        </div>
                    </div>
                    <div class="payment-information">
                        <h6 class="payment-information__title mb-2">@lang('Description')</h6>
                        <span class="text description"></span>
                        <button class="see-more-btn mt-2">@lang('See more')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let totalPrice = 0;
            let totalOneNightPrice = 0;
            let totalTax = 0;
            let totalTaxPerNight = 0;
            let oldPrice = 0;
            let price = 0;
            let image = '';
            let roomTypeName = '';
            let tempTotalAdult = 0;
            let tempTotalChild = 0;
            let selectedTotalAdult = 0;
            let selectedTotalChild = 0;
            let selectedTotalRoom = 0;
            let roomTypeId = '';
            let roomTypeIds = [];
            let roomTypeCount = {};
            let hotelTaxes = '{{ $hotelTaxPercentage }}';
            let currency = `{{ gs('cur_sym') }}`;
            let totalRoomCount = 0;
            let availableRoomCount = 0;

            let checkIn = $('[name="check_in"]').val();
            let checkOut = $('[name="check_out"]').val();
            let totalNight = (new Date(checkOut).getTime() - new Date(checkIn).getTime()) / (1000 * 3600 * 24);
            let rooms = $('[name="rooms"]').val();

            $('.total_stays').text(totalNight);


            function getRoomCard() {
                return `<div class="card-box">
                            <div class="card-box__thumb">
                                <img src="${image}" alt="room image" class="fit-image">
                            </div>
                            <div class="card-box__content">
                                <div class="item">
                                    <h6 class="title"> ${roomTypeName}</h6>
                                    <span class="text fw-500">
                                        <i class="las la-user"></i>
                                        ${tempTotalAdult} @lang('Adult'),
                                        ${ tempTotalChild } @lang('Children')
                                    </span>
                                </div>
                                <div class="item">
                                    <h6 class="title">
                                        <strike class="text--danger">${oldPrice > 0 ? currency + Number(oldPrice).toFixed(2) : ''}</strike>
                                        ${currency}${Number(price).toFixed(2)}
                                    </h6>
                                    <span class="text">${Number(hotelTaxes).toFixed(2)}% @lang('Taxes & Fees') </span>
                                </div>
                            </div>
                            <button type="button" class="delete-btn card-box__btn removeRoomButton" data-room_type_id="${roomTypeId}" data-old_price="${oldPrice}" data-price="${price}" data-available_room_count="${availableRoomCount}" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-title="Delete">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>`;
            }

            function calculateAvailableRoomCount(action = 'add') {
                if (action == 'add') {
                    if (!roomTypeIds.includes(roomTypeId)) {
                        totalRoomCount += 1;
                        if (totalRoomCount == 1) {
                            $('.no-room').hide();
                        }
                        roomTypeIds.push(roomTypeId);
                        roomTypeCount[roomTypeId] = 1;
                        $('[name="room_type_ids"]').val(JSON.stringify(roomTypeCount));
                    } else {
                        totalRoomCount += 1;
                        roomTypeCount[roomTypeId] += 1;
                        $('[name="room_type_ids"]').val(JSON.stringify(roomTypeCount));
                    }
                    if (roomTypeCount[roomTypeId] > availableRoomCount) {
                        $('#roomType_' + roomTypeId).addClass('disabled');
                        return false;
                    }
                    if (roomTypeCount[roomTypeId] == availableRoomCount) {
                        $('#roomType_' + roomTypeId).addClass('disabled');
                    }
                } else {
                    totalRoomCount -= 1;
                    roomTypeCount[roomTypeId] -= 1;
                    if (roomTypeCount[roomTypeId] < availableRoomCount) {
                        $('#roomType_' + roomTypeId).removeClass('disabled');
                    }
                    if (roomTypeCount[roomTypeId] == 0) {
                        roomTypeIds = roomTypeIds.filter(id => id !== roomTypeId);
                        delete roomTypeCount[roomTypeId];
                        $('[name="room_type_ids"]').val(JSON.stringify(roomTypeCount));
                    }
                    if (totalRoomCount <= 0) {
                        $('.no-room').show();
                    }
                }
                return true;
            }

            function filterHotels() {
                let form = $('.booking-filter__form');
                let url = "{{ route('hotel.details', $hotel->id) }}";
                let formData = form.serialize();
                let fullUrl = `${url}?${formData}`;
                $.ajax({
                    url: fullUrl,
                    type: 'GET',
                    data: {
                        filter: true
                    },
                    success: function(response) {
                        $('.roomTypeWrapper').html(response.view);
                        totalNight = (new Date(checkOut).getTime() - new Date(checkIn).getTime()) / (1000 *
                            3600 * 24);

                        totalPrice = Number(totalOneNightPrice * totalNight);
                        totalTax = Number(totalTaxPerNight * totalNight);

                        $('.totalPrice').text(currency + Number(totalPrice + totalTax).toFixed(2));
                        $('.taxes_fees').text(currency + Number(totalTax).toFixed(2));

                        enableBookingButton();
                        disableBookingButton();

                        $('.total_stays').text(totalNight);

                        setTimeout(function() {
                            $('.skeleton').removeClass('skeleton');
                        }, 1000);
                        window.history.pushState({}, '', fullUrl);
                    }
                });
            }

            function enableBookingButton() {
                if (Number($('.roomCount').text()) === selectedTotalRoom) {
                    $('.bookingContinueBtn').attr('disabled', false);
                    $('.addRoom').addClass('d-none');
                }
            }

            function disableBookingButton() {
                if (Number($('.roomCount').text()) !== selectedTotalRoom) {
                    $('.bookingContinueBtn').attr('disabled', true);
                    $('.addRoom').removeClass('d-none');
                }
            }

            function loadPage() {
                $("#products-overlay").fadeIn(100);
                $("html, body").animate({
                    scrollTop: $("#search-section").offset().top - 100
                }, {
                    duration: 800,
                    complete: function() {
                        setTimeout(function() {
                            $("#products-overlay").fadeOut(500);
                        }, 1000);
                    }
                });
            }

            $('.bookingContinueBtn').on('click', function(e) {
                e.preventDefault();
                if (Number($('.roomCount').text()) !== selectedTotalRoom) {
                    return false;
                }
                let form = $(this).closest('form');
                form.find('[name=checkin]').val($('[name="check_in"]').val());
                form.find('[name=checkout]').val($('[name="check_out"]').val());
                $('.adultQty').each(function() {
                    selectedTotalAdult += parseInt($(this).text(), 10);
                });
                $('.childQty').each(function() {
                    selectedTotalChild += parseInt($(this).text(), 10);
                });
                form.find('[name=total_adult]').val(selectedTotalAdult);
                form.find('[name=total_child]').val(selectedTotalChild);
                form.submit();
            });

            $(document).on('click', '.addRoom', function() {
                if (Number($('.roomCount').text()) <= selectedTotalRoom) return false;
                roomTypeId = $(this).data('room_type_id');
                roomTypeName = $(this).data('room_type_name');
                tempTotalAdult = $(this).data('total_adult');
                tempTotalChild = $(this).data('total_child');
                image = $(this).data('image');
                oldPrice = $(this).data('old_price');
                price = $(this).data('price');
                availableRoomCount = $(this).data('available_room_count');
                if (!calculateAvailableRoomCount('add')) return false;

                totalPrice += Number(price * totalNight);
                let tax = Number((price * hotelTaxes) / 100);
                totalTax += Number(tax * totalNight);
                totalOneNightPrice += Number(price);
                totalTaxPerNight += Number(tax);

                $('.totalPrice').text(currency + Number((totalPrice) + totalTax).toFixed(2));
                $('.taxes_fees').text(currency + Number(totalTax).toFixed(2));
                $('.room-summery').append(getRoomCard());

                selectedTotalRoom++;
                enableBookingButton();
            });

            $(document).on('click', '.removeRoomButton', function() {
                $(this).parents('.card-box').remove();
                roomTypeId = $(this).data('room_type_id');
                oldPrice = $(this).data('old_price');
                price = $(this).data('price');
                availableRoomCount = $(this).data('available_room_count');
                calculateAvailableRoomCount('remove');

                totalPrice -= Number(price * totalNight);
                let tax = Number((price * hotelTaxes) / 100);
                totalTax -= Number(tax * totalNight);
                totalOneNightPrice -= Number(price);
                totalTaxPerNight -= Number(tax);

                $('.totalPrice').text(currency + Number(totalPrice + totalTax).toFixed(2));
                $('.taxes_fees').text(currency + Number(totalTax).toFixed(2));

                selectedTotalRoom--;
                disableBookingButton();
            });

            $('.reply-btn').on('click', function() {
                $('.comment-form-wrapper').not($(this).closest('.comment-reply').find('.comment-form-wrapper'))
                    .slideUp();
                $(this).closest('.comment-reply').find('.comment-form-wrapper').slideToggle();
            });

            $('.hotel-details__thumb').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                Infinity: true,
                centerMode: true,
                autoplay: true,
                autoplaySpeed: 2000,
                speed: 1500,
                prevArrow: '<button type="button" class="slick-prev"><i class="las la-angle-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="las la-angle-right"></i></button>',
            });

            $(document).on('click', '#commonFilter', function(e) {
                e.preventDefault();
                rooms = $('[name="rooms"]').val();
                checkIn = $('[name="check_in"]').val();
                checkOut = $('[name="check_out"]').val();
                if (checkOut == 'null') {
                    notify('error', 'Please select check out date.');
                } else {
                    loadPage();
                    filterHotels();
                }
            });

            setTimeout(function() {
                $('.skeleton').removeClass('skeleton');
            }, 1000);

            $(document).on('click', '.roomDetailBtn', function() {
                let modal = $('#roomDetailModel');
                let roomTypeData = $(this).data('room_type');
                let beds = Object.entries($(this).data('beds'));
                let amenities = $(this).data('amenities');
                let facilities = $(this).data('facilities');

                modal.find('.roomTypeImage').attr('src', $(this).data('image'));
                modal.find('.hotel-room-details__title').text(roomTypeData.name);

                let html = '<i class="las la-bed"></i> ';
                beds.forEach(([bedType, quantity], index) => {
                    html += `${quantity} ${bedType}`;
                    if (index !== beds.length - 1) {
                        html += ' | ';
                    }
                });
                modal.find('.bed').html(html);
                modal.find('.capacity').html(
                    `<i class="las la-users"></i> ${roomTypeData.total_adult} Adult, ${roomTypeData.total_child} Children`
                );
                modal.find('.description').text(roomTypeData.description);

                html = '';
                if (amenities.length > 0) {
                    amenities.forEach(function(amenity) {
                        html += `<li class="payment-list__item">
                                    <span class="payment-list__text">${amenity.title}</span>
                                </li>`;
                    });
                }
                modal.find('.amenities').html(html);
                html = '';
                if (facilities.length > 0) {
                    facilities.forEach(function(facility) {
                        html += `<li class="payment-list__item">
                                    <span class="payment-list__text">${facility.name}</span>
                                </li>`;
                    });
                }
                modal.find('.facilities').html(html);
                modal.find('.cancellationFee').text(
                    `${currency}${Number(roomTypeData.cancellation_fee).toFixed(2)}`);
                modal.find('.availableRooms').text($(this).data('available_room_count'));

                let totalFarePerNight = Number(roomTypeData.fare);
                modal.find('.farePerNight').text(`${currency}${totalFarePerNight.toFixed(2)}`);
                let discountedPercentage = Number(roomTypeData.discount_percentage);
                let discountedFarePerNight = 0;
                if (discountedPercentage > 0) {
                    discountedFarePerNight = totalFarePerNight - (totalFarePerNight * discountedPercentage /
                        100);
                    totalFarePerNight -= (totalFarePerNight * discountedPercentage / 100);
                    modal.find('.discountedPercentage').text(`${discountedPercentage.toFixed(2)}%`);
                    modal.find('.discountedFarePerNight').text(`${currency}${totalFarePerNight.toFixed(2)}`);
                } else {
                    discountedFarePerNight = totalFarePerNight - (totalFarePerNight * discountedPercentage /
                        100);
                    modal.find('.discountedPercentage').text(`${0}%`);
                    modal.find('.discountedFarePerNight').text(`${currency}${totalFarePerNight.toFixed(2)}`);
                }
                let totalTax = (Number($(this).data('tax')) / 100) * discountedFarePerNight;
                modal.find('.taxesFees').text(`${currency}${totalTax.toFixed(2)}`);
                modal.find('.totalFarePerNight').text(
                    `${currency}${(totalFarePerNight + totalTax).toFixed(2)}`);
                modal.modal('show');
            });

            $(document).ready(function() {
                $('.see-more-btn').click(function() {
                    $('.description').toggleClass('expanded');
                    var btnText = $(this).text() === 'See more' ? 'See less' : 'See more';
                    $(this).text(btnText);
                });
            });

            $('.popup-img').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .booking-card__item img {
            width: 15px;
            margin-right: 5px;
        }

        .booking-card__item {
            font-weight: 500;
            color: hsl(var(--base));
        }

        .room-summery {
            min-height: 300px;
        }

        .booking-filter__left {
            grid-template-columns: repeat(2, 1fr);
        }

        @media screen and (max-width: 767px) {
            .booking-filter__left {
                grid-template-columns: repeat(1, 1fr);
                gap: 16px;
            }
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


        /*======= comment css start here =======*/
        .comment-form-wrapper {
            position: relative;
            margin-bottom: 16px;
            margin-top: 20px;
            display: none;
        }

        .comment-form-wrapper .comment-author {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            position: absolute;
            left: 0;
            top: -3px;
        }

        .comment-form-wrapper .comment-author img {
            width: 100%;
            height: 100%;
        }

        .comment-form .form-group {
            text-align: right;
            margin-bottom: 0;
        }

        .comment-form .form--control {
            border: 0;
            border-bottom: 1px solid hsl(var(--black) / 0.1);
            border-radius: 0;
            padding: 0;
            font-size: 0.875rem;
            color: hsl(var(--black));
            background-color: transparent;
            padding-right: 60px;
            display: block;
            height: unset;
        }

        .comment-form .comment-btn {
            color: hsl(var(--black));
            background: transparent;
            font-size: 1rem;
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: 40px;
        }

        .comment-form .form-group::after {
            content: "";
            display: block;
            width: 0;
            height: 1px;
            background: hsl(var(--black));
            position: absolute;
            bottom: -1px;
            left: 0;
            transition: all 0.1s linear;
        }

        .comment-form .form-group:has(.form--control:focus)::after {
            width: 100%;
        }

        .comment-box-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            position: relative;
        }

        .comment-box-item__thumb {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        .comment-box-item__thumb img {
            width: 100%;
            height: 100%;
        }

        .comment-box-item__content {
            width: calc(100% - 40px);
        }

        .comment-box-item__name {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
            font-size: 13px;
            color: hsl(var(--black) / .9);
        }

        .comment-box-item__name .time {
            font-size: 12px;
            color: hsl(var(--body-color));
            display: block;
        }

        .comment-box-item__text {
            font-size: 14px;
            color: hsl(var(--black));
            font-weight: 500;
        }

        .comment-box__content {
            margin-top: 24px;
        }

        .comment-form .form--control {
            line-height: inherit;
        }

        .info-wrapper {
            border-bottom: 1px solid hsl(var(--black) / 0.1);
            border-top: 1px solid hsl(var(--black) / 0.1);
            padding: 12px 0;
            gap: 100px;
            margin-top: 12px;
            justify-content: space-between;

            .payment-information {
                border: 0;
                padding: 0;
                background: transparent;
                margin-top: 0;

                .payment-list {
                    border-bottom: 0;
                    padding-top: 0px;
                }
            }

            .payment-list__item:last-child {
                margin-bottom: 0;
            }
        }

        .description.text {
            display: block;
            overflow: hidden;
            line-height: 1.6em;
            max-height: 26px;
            transition: max-height 0.4s ease;
            font-size: 14px;
            font-weight: 300;
        }

        .description.expanded {
            max-height: 1000px;
        }

        .see-more-btn {
            font-size: 14px;
            font-weight: 400;
            color: hsl(var(--base));
        }

        .payment-information .payment-list__text {
            font-weight: 300;
            color: hsl(var(--body-color));
        }

        .hotel-room-details__text {
            font-weight: 300;
        }

        .info-wrapper:last-child {
            border-bottom: 1px solid hsl(var(--black) / 0.1)
        }

        /*========== comment css end here ==========*/

        .products-overlay-container {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            height: 100%;
            width: 100%;
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
        }

        #overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .cv-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid hsl(var(--black) / .2);
            border-top: 4px solid hsl(var(--base));
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }

        .hotel-details__item .facility-wrapper .facility-list__item::after{
            border-radius: 4px;
        }
    </style>
@endpush
