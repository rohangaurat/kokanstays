@extends('owner.layouts.app')
@section('panel')
    @if (authOwner()->parent_id == 0 && (authOwner()->expire_at == null || authOwner()->expire_at < now()->format('Y-m-d')))
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bl--5-danger">
                    <div class="card-body">
                        <p class="fw-bold text--danger">@lang('Bill Payment Alert')</p>
                        <p>@lang('You won\'t be able to use the system without making your monthly bill payment. You can make your monthly bill payment') <a href="{{ route('owner.deposit.index') }}">@lang('here')...</a></p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget color="danger" icon="la la-sign-out transform-rotate-180" overlay_icon="0" cover_cursor="1"
                link="owner.delayed.booking.checkout" style="2" title="Delayed Checkout"
                value="{{ $widget['delayed_checkout'] }}" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget color="warning" icon="la la-sign-in" link="owner.pending.booking.checkin" style="2"
                overlay_icon="0" cover_cursor="1" title="Pending Check-In" value="{{ $widget['pending_checkin'] }}" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget color="info" icon="la la-sign-in" link="owner.upcoming.booking.checkin" style="2"
                overlay_icon="0" cover_cursor="1" title="Upcoming Check-In" value="{{ $widget['upcoming_checkin'] }}" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget color="info" icon="la la-sign-out transform-rotate-180" link="owner.upcoming.booking.checkout"
                style="2" overlay_icon="0" cover_cursor="1" title="Upcoming Checkout"
                value="{{ $widget['upcoming_checkout'] }}" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget color="dark" icon="la la-check-circle" icon_style="false" link="owner.booking.todays.booked"
                style="2" overlay_icon="0" cover_cursor="1" title="Today's Booked Rooms"
                value="{{ $widget['today_booked'] }}" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget color="info" icon="la la-hospital-alt" icon_style="false" link="owner.booking.todays.booked"
                query_string="type=not_booked" style="2" overlay_icon="0" cover_cursor="1"
                title="Today's Available Rooms" value="{{ $widget['today_available'] }}" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget color="success" icon="la la-clipboard-check" icon_style="false" link="owner.booking.active"
                style="2" overlay_icon="0" cover_cursor="1" title="Active Booking" value="{{ $widget['active'] }}" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget color="primary" icon="la la-city" icon_style="false" link="owner.booking.all" style="2"
                overlay_icon="0" cover_cursor="1" title="Total Bookings" value="{{ $widget['total'] }}" />
        </div>
    </div>
    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div class="d-flex justify-content-start align-items-center gap-1">
                            <h5 class="card-title mb-0">@lang('Booking Report')</h5>
                            <span class="text--small fw-bold">(@lang('Excluding Tax'))</span>
                        </div>
                        <div id="bookingDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="bookingReportArea"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Payment Report')</h5>
                        <div id="paymentDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="paymentReportArea"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (authOwner()->expire_at)
    @php
        $expire = \Carbon\Carbon::parse(authOwner()->expire_at);
        $daysLeft = now()->diffInDays($expire, false);
    @endphp

    @push('breadcrumb-plugins')
        @if($expire->isFuture())

    @if($daysLeft <= 5)
        <div class="alert alert-warning">
            ⚠ @lang('Your subscription expires in') {{ (int)$daysLeft }} @lang('days').
            <a href="{{ route('owner.deposit.index') }}" class="fw-bold">@lang('Renew now')</a>
        </div>
    @else
        <div class="alert custom--alert">
            @lang('Subscription active until') {{ showDateTime($expire, 'd M, Y') }}
            ({{ (int)$daysLeft }} @lang('days left'))
        </div>
    @endif

@else
    <div class="alert alert-danger">
        @lang('Subscription expired') {{ showDateTime($expire, 'd M, Y') }}
    </div>
@endif
    @endpush
@endif

@push('script-lib')
    <script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/global/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        const start = moment().subtract(14, 'days');
        const end = moment();

        const dateRangeOptions = {
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')],
                'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment()
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
        }

        let bookingReport = barChart(
            document.querySelector("#bookingReportArea"),
            @json(__(gs('cur_text'))),
            [{
                name: 'Payment',
                data: []
            }],
            [],
        );

        let paymentReport = lineChart(
            document.querySelector("#paymentReportArea"),
            [{
                    name: "Plus Transactions",
                    data: []
                },
                {
                    name: "Minus Transactions",
                    data: []
                }
            ],
            []
        );

        const bookingReportChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }

            const url = @json(route('owner.chart.booking'));
            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        bookingReport.updateSeries(data.data);
                        bookingReport.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        const paymentReportChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }

            const url = @json(route('owner.chart.payment'));
            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        paymentReport.updateSeries(data.data);
                        paymentReport.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        $('#bookingDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText(
            '#bookingDatePicker span', start, end));
        $('#paymentDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText(
            '#paymentDatePicker span', start, end));

        changeDatePickerText('#bookingDatePicker span', start, end);
        changeDatePickerText('#paymentDatePicker span', start, end);

        bookingReportChart(start, end);
        paymentReportChart(start, end);

        $('#bookingDatePicker').on('apply.daterangepicker', (event, picker) => bookingReportChart(picker.startDate, picker
            .endDate));
        $('#paymentDatePicker').on('apply.daterangepicker', (event, picker) => paymentReportChart(picker.startDate, picker
            .endDate));
    </script>
@endpush

@push('style')
    <style>
        .custom--alert {
            background-color: #ff9e430d;
            color: #ff9f43;
            padding: 5px 10px;
            border-left: 3px solid #ff9f43;
            font-size: 14px;
            font-weight: 400;
        }
    </style>
@endpush
