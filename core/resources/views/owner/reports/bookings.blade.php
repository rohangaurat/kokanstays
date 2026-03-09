@extends('owner.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="row gy-4">
                <div class="col-xxl-3 col-sm-6">
                    <x-widget overlay_icon="0" color="primary" icon="las la-list" style="2" icon_style="solid" title="Total Bookings" value="{{ $insights['total_bookings'] }}" />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget overlay_icon="0" color="dark" icon="las la-money-bill" style="2" icon_style="solid" title="Total Amount" value="{{ showAmount($insights['total_amount']) }}" />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget overlay_icon="0" color="success" icon="las la-wallet" style="2" icon_style="solid" title="Total Paid Amount" value="{{ showAmount($insights['paid_amount']) }}" />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget overlay_icon="0" color="danger" icon="las la-hand-holding-usd" style="2" icon_style="solid" title="Total Due Amount" value="{{ showAmount($insights['due_amount']) }}" />
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Booking Number')</th>
                                    <th>@lang('Booked At')</th>
                                    <th>@lang('Check-In')</th>
                                    <th>@lang('Checkout')</th>
                                    <th>@lang('Guest')</th>
                                    <th>@lang('Days')</th>
                                    <th>@lang('Rooms')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Paid')</th>
                                    <th>@lang('Due')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td><a href="{{ route('owner.booking.details', $booking->id) }}">{{ $booking->booking_number }}</a></td>
                                        <td>{{ showDateTime($booking->created_at, 'd M, Y h:iA') }}</td>
                                        <td>{{ showDateTime($booking->check_in, 'd M, Y') }}</td>
                                        <td>{{ showDateTime($booking->check_out, 'd M, Y') }}</td>
                                        <td>
                                            @if ($booking->user_id)
                                                <span class="fw-bold">{{ $booking->user->fullname }}</span>
                                            @elseif($booking->guest_id)
                                                <span class="fw-bold">{{ $booking->guest->name }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $booking->stayingDays() }}</td>
                                        <td>{{ $booking->bookedRooms->count() }}</td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($booking->total_amount) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text--success">
                                                {{ showAmount($booking->paid_amount) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text--danger">
                                                {{ showAmount($booking->due_amount) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($bookings->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($bookings) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">@lang('Filter Bookings')</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="GET">
                <div class="form-group">
                    <label>@lang('Check-In')</label>
                    <input autocomplete="off" class="check_in form-control" data-language="en" data-multiple-dates-separator=" - " data-position='bottom right' data-range="true" name="check_in" type="text" value="{{ request()->check_in }}">
                </div>
                <div class="form-group">
                    <label>@lang('Checkout')</label>
                    <input autocomplete="off" class="checkout form-control" data-language="en" data-multiple-dates-separator=" - " data-position='bottom right' data-range="true" name="check_out" type="text" value="{{ request()->check_out }}">
                </div>
                <div class="form-group">
                    <label>@lang('Booked On')</label>
                    <input autocomplete="off" class="created_at form-control" data-language="en" data-multiple-dates-separator=" - " data-position='bottom right' data-range="true" name="created_at" type="text" value="{{ request()->created_at }}">
                </div>
                <div class="form-group">
                    <label>@lang('Booking Number')</label>
                    <input class="form-control" name="booking_number" placeholder="@lang('Booking Number')" type="text" value="{{ request()->booking_number }}">
                </div>
                <div class="form-group">
                    <label>@lang('Guest')</label>
                    <input class="form-control" name="guest" type="text" placeholder="@lang('Name / Email')" value="{{ request()->guest }}">
                </div>
                <div class="form-group">
                    <label>@lang('Room Type')</label>
                    <select name="room_type_id" class="form-control select2" data-minimum-results-for-search="-1">
                        <option value="">@lang('Any')</option>
                        @foreach ($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" @selected(request()->room_type_id == $roomType->id)>{{ __($roomType->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Room')</label>
                    <select name="room_number" class="form-control select2" data-minimum-results-for-search="-1">
                        <option value="">@lang('Any')</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->room_number }}" @selected(request()->room_number == $room->room_number)>{{ $room->room_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow-1 align-self-end">
                    <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn--primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"> <i class="las la-filter"></i>@lang('Filter')</button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            const datePickerOptions = {
                autoUpdateInput: false,
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment(),
                locale: {
                    cancelLabel: 'Clear'
                }
            }

            $('.check_in').daterangepicker(datePickerOptions);
            $('.checkout').daterangepicker(datePickerOptions);
            $('.created_at').daterangepicker(datePickerOptions);

            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MM/DD/YYYY') + ' - ' + endDate.format('MM/DD/YYYY'));
            }

            $('.check_in').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));
            $('.checkout').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));
            $('.created_at').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));


            if ($('.check_in').val()) {
                let dateRange = $('.check_in').val().split(' - ');
                $('.check_in').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.check_in').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }
            if ($('.checkout').val()) {
                let dateRange = $('.checkout').val().split(' - ');
                $('.checkout').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.checkout').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }
            if ($('.created_at').val()) {
                let dateRange = $('.created_at').val().split(' - ');
                $('.created_at').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.created_at').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .btn-close:focus {
            outline: 0;
            box-shadow: none;
        }

        .datepickers-container {
            z-index: 9999 !important;
        }
    </style>
@endpush
