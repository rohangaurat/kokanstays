@extends('owner.layouts.app')
@section('panel')
    <div class="row gy-4 booking-wrapper">
        <div class="col-xxl-8 col-xl-6">
            <div class="row gy-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Booking Information')</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-3 justify-content-between mb-1">
                                <div class="d-flex flex-column mb-3">
                                    <h6>{{ $bookingRequest->user->fullname }}</h6>
                                    <small class="text-muted">@lang('Guest Name')</small>
                                </div>
                                <div class="d-flex flex-column mb-3">
                                    <h6>+{{ $bookingRequest->user->mobile }}</h6>
                                    <small class="text-muted">@lang('Mobile')</small>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6>{{ $bookingRequest->user->email }}</h6>
                                    <small class="text-muted">@lang('Email')</small>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6>{{ @$bookingRequest->contact_info->name }}</h6>
                                    <small class="text-muted">@lang('Contact Name')</small>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6>{{ @$bookingRequest->contact_info->phone }}</h6>
                                    <small class="text-muted">@lang('Contact Number')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="bookingInfo row gy-4"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title mb-0">
                        <h5>@lang('Book Room')</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.request.booking.assign.room') }}" class="booking-form" method="POST">
                        @csrf
                        <input name="booking_request_id" type="hidden" value="{{ $bookingRequest->id }}">
                        <div class="orderList d-none">
                            <ul class="list-group list-group-flush orderItem">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <h6>@lang('Room')</h6>
                                    <h6>@lang('Days')</h6>
                                    <h6>@lang('Fare')</h6>
                                    <h6>@lang('Total')</h6>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center border-top p-2 px-3">
                                <span>@lang('Subtotal')</span>
                                <span class="totalFare" data-amount="0"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top p-2 px-3 discountLi">
                                <span>@lang('Discount')</span>
                                <span class="totalDiscount" data-discount="0"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top p-2 px-3">
                                <span>{{ @$bookingRequest->owner->hotelSetting->tax_name }}
                                    <small>({{ $bookingRequest->taxPercentage() }}%)</small></span>
                                <span><span class="taxCharge"
                                        data-percent_charge="{{ $bookingRequest->taxPercentage() }}">{{ showAmount($bookingRequest->tax_charge, currencyFormat: false) }}</span>
                                    {{ gs()->cur_text }}</span>
                                <input name="tax_charge" type="hidden">
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top p-2 px-3">
                                <span>@lang('Total Fare')</span>
                                <span class="grandTotalFare"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Paying Amount')</label>
                            <div class="input-group">
                                <input class="form-control" min="0" name="paid_amount" step="any" type="number">
                                <span class="input-group-text">{{ __(gs()->cur_text) }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Payment System')</label>
                            @can('owner.hotel.setting.payment.systems')
                                @if (!$paymentSystems->count())
                                    <small class="text-muted text--small">@lang("You don't have any payment system.") <a class="text--small"
                                            href="{{ route('owner.hotel.setting.payment.systems') }}">@lang('Add Now')</a>
                                    </small>
                                @endif
                            @endcan
                            <select class="form-control" name="payment_system_id">
                                <option value="">@lang('Select One')</option>
                                @foreach ($paymentSystems as $paymentSystem)
                                    <option value="{{ $paymentSystem->id }}">{{ __($paymentSystem->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @can('owner.request.booking.assign.room')
                            <button class="btn btn--primary w-100 h-45 btn-book confirmBookingBtn"
                                type="button">@lang('Book Now')</button>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmBookingModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure to book this rooms?')</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--dark" data-bs-dismiss="modal" type="button">@lang('No')</button>
                    <button class="btn btn--primary btn-confirm" type="button">@lang('Yes')</button>
                </div>
            </div>
        </div>
    </div>

    @can('owner.request.booking.cancel')
        <x-confirmation-modal />
    @endcan
@endsection

@push('breadcrumb-plugins')
    @can('owner.request.booking.cancel')
        <button class="btn btn-sm btn-outline--danger confirmationBtn"
            data-action="{{ route('owner.request.booking.cancel', $bookingRequest->id) }}" data-question="@lang('Are you sure, you want to cancel this booking request?')">
            <i class="las la-times-circle"></i>@lang('Cancel')
        </button>
    @endcan
    @can('owner.book.room')
        <a href="{{ route('owner.book.room', $bookingRequest->id) }}" class="btn btn-sm btn-outline--primary"
            target="_blank">
            <i class="la la-hand-o-right"></i>@lang('Book Room')
        </a>
    @endcan
@endpush

@push('style')
    <style>
        .booking-table td {
            white-space: unset;
        }
    </style>
@endpush

@push('script')
    <script src="{{ asset('assets/owner/js/booking.js') }}"></script>
    <script>
        (function($) {
            "use strict";

            let roomListHtml = @json($view);
            $('.bookingInfo').html(roomListHtml);

            updateOrderList();

            $('[name=paid_amount]').on('keypress', function(e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                    $('.confirmBookingBtn').click();
                }
            });
        })(jQuery);
    </script>
@endpush
