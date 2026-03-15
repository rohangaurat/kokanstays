@extends('Template::layouts.master')
@section('content')
    @if (!blank($bookings))
        <div class="booking-main-wrapper">
            <div class="row gy-4">
                @foreach ($bookings as $booking)
                    {{-- Custom Fix (KokanStays): correct due/refund logic for booking history --}}
@php
$due = $booking->total_amount - $booking->paid_amount;

$isRefundable = $due < 0;
$displayAmount = abs($due);
@endphp

@include('Template::partials.booking_card', [
    'booking' => $booking,
    'detailsRoute' => 'user.booking.details',
    'badge' => $booking->statusCustomBadge,
    'bookingId' => $booking->booking_number ?? null,
    'bookAgain' => false,
    'dueAmount' => $displayAmount,
    'isRefundable' => $isRefundable,
])
                @endforeach
            </div>
            @if ($bookings->hasPages())
                <div class="mt-4">{{ paginateLinks($bookings) }}</div>

            @endif
        </div>
    @else
        <div class="card custom--card">
            <div class="card-body">
                @include('Template::partials.empty_list', ['message' => 'No booking found.'])
            </div>
        </div>
    @endif
@endsection

@push('modal')
    <div class="modal fade custom--modal" id="payNowModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">@lang('Hotel Details')</h6>
                    <button type="button" class="close-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="hotel-room-details">
                        <div class="hotel-room-details__item d-flex justify-content-between gap-3 align-items-center pt-3">
                            <div>
                                <p class="hotel-room-details__title"></p>
                                <span class="hotel-room-details__text"></span>
                            </div>
                            <div class="hotel-room-details__right">
                                <div class="hotel-room-details__thumb">
                                    <img src="" alt="" class="fit-image hotelImage">
                                </div>
                            </div>
                        </div>
                        <div class="hotel-room-details__item">
                            <ul class="booking-system">
                                <li class="booking-system__item flex-between">
                                    <div class="left">
                                        <span class="text">@lang('Booked')</span>
                                        <p class="time bookedAt"></p>
                                    </div>
                                    <div class="right">
                                        <span class="text">@lang('For')</span>
                                        <p class="time nightStay"></p>
                                    </div>
                                </li>
                                <li class="booking-system__item flex-between">
                                    <div class="left">
                                        <span class="text">@lang('Check In')</span>
                                        <p class="time checkIn"></p>
                                    </div>
                                    <div class="right">
                                        <span class="text">@lang('Check Out')</span>
                                        <p class="time checkOut"></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="hotel-room-details__item table-item">
                            <h6 class="details-title">@lang('Room Details')</h6>
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Room Type')</th>
                                        <th>@lang('Room No')</th>
                                        <th>@lang('Fare/Night')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="Room Type"> Single Room </td>
                                        <td data-label="Room No"> 54 </td>
                                        <td data-label="Night"> 150.00 USD </td>
                                    </tr>
                                    <tr>
                                        <td data-label="Room Type"> King Bed Room </td>
                                        <td data-label="Room No"> 302 </td>
                                        <td data-label="Night"> 857.00 USD </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="payment-information">
                        <h6 class="payment-information__title"> Payment Information </h6>
                        <span class="payment-information__text"> Fare Summery </span>
                        <ul class="payment-list">
                            <li class="payment-list__item">
                                <span class="payment-list__text"> Total Fare </span>
                                <span class="payment-list__price"> 0.00 USD </span>
                            </li>
                            <li class="payment-list__item">
                                <span class="payment-list__text"> Discount </span>
                                <span class="payment-list__price"> 0.00 USD </span>
                            </li>
                            <li class="payment-list__item item-two">
                                <span class="payment-list__text"> Subtotal </span>
                                <span class="payment-list__price"> 0.00 USD </span>
                            </li>
                        </ul>
                        <ul class="payment-list">
                            <li class="payment-list__item">
                                <span class="payment-list__text"> GST(0.00%) </span>
                                <span class="payment-list__price"> +0.00 USD </span>
                            </li>
                            <li class="payment-list__item">
                                <span class="payment-list__text"> Canceled Fare </span>
                                <span class="payment-list__price"> -10.00 USD </span>
                            </li>
                            <li class="payment-list__item">
                                <span class="payment-list__text"> Canceled GST </span>
                                <span class="payment-list__price"> +0.00 USD </span>
                            </li>
                            <li class="payment-list__item item-two">
                                <span class="payment-list__text"> Total Amount </span>
                                <span class="payment-list__price"> = 10.00 USD </span>
                            </li>
                        </ul>
                        <ul class="payment-list">
                            <li class="payment-list__item">
                                <span class="payment-list__text"> Payment Received </span>
                                <span class="payment-list__price"> 1.00 USD </span>
                            </li>
                            <li class="payment-list__item">
                                <span class="payment-list__text"> Refunded </span>
                                <span class="payment-list__price"> 0.00 USD </span>
                            </li>
                        </ul>
                        <div class="payment-information__bottom">
                            <span class="total-payment">Receivable form user </span>
                            <span class="total-payment"> = 9.00 USD </span>
                        </div>
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

            $('.payNowBtn').on('click', function() {
                let modal = $('#payNowModal');
                let booking = $(this).data('booking');
                let bookedRooms = booking.booked_rooms;
                modal.find('.hotel-room-details__title').text(booking.owner.hotel_setting.name);
                let html = `<i class="las la-map-marked"></i> ${booking.owner.hotel_setting.hotel_address}`;
                modal.find('.hotel-room-details__text').html(html);
                modal.find('.hotelImage').attr('src', $(this).data('image'));
                modal.find('.bookedAt').text($(this).data('booked_at'));
                modal.find('.nightStay').text($(this).data('night_stay') + ' ' + "@lang('night')");
                modal.find('.checkIn').text($(this).data('check_in'));
                modal.find('.checkOut').text($(this).data('check_out'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .booking-card.card-three .booking-card__content-inner {
            align-items: flex-start;
        }

        .booking-card.card-three .booking-card__right .price {
            font-size: 16px;
        }
    </style>
@endpush
