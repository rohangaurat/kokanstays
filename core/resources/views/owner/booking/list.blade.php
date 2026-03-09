@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button class="btn btn-outline--primary showFilterBtn btn-sm" type="button"><i class="las la-filter"></i> @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Keywords') <i class="las la-info-circle text--info" title="@lang('Search by booking number, username or email')"></i></label>
                                <input class="form-control" name="search" type="text" value="{{ request('search') }}">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Check In')</label>
                                <input autocomplete="off" class="check_in form-control" data-language="en" data-position='bottom right' data-range="false" name="check_in" type="text" value="{{ request('check_in') }}">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Checkout')</label>
                                <input autocomplete="off" class="checkout form-control" name="check_out" type="text" value="{{ request('check_out') }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card bg--transparent">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table bg-white">
                            <thead>
                                <tr>
                                    <th>@lang('Booking Number')</th>
                                    <th>@lang('Guest')</th>
                                    <th>@lang('Check In') | @lang('Check Out')</th>
                                    <th>@lang('Total Amount')</th>
                                    <th>@lang('Total Paid')</th>
                                    <th>@lang('Due')</th>
                                    @if (request()->routeIs('owner.booking.all') || request()->routeIs('owner.booking.active'))
                                        <th>@lang('Status')</th>
                                    @endif
                                    @can(['owner.booking.details', 'owner.booking.booked.rooms', 'owner.booking.service.details', 'owner.booking.payment', 'owner.booking.key.handover', 'owner.booking.merge', 'owner.booking.cancel', 'owner.booking.extra.charge', 'owner.booking.checkout', 'owner.booking.invoice'])
                                        <th>@lang('Action')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr class="@if ($booking->isDelayed() && !request()->routeIs('owner.booking.checkout.delayed')) delayed-checkout @endif">
                                        <td>
                                            @if ($booking->key_status)
                                                <span class="text--warning ">
                                                    <i class="las la-key f-size--24"></i>
                                                </span>
                                            @endif
                                            <span class="fw-bold">{{ $booking->booking_number }}</span><br>
                                            <em class="text-muted text--small">{{ showDateTime($booking->created_at, 'd M, Y h:i A') }}</em>
                                        </td>
                                        <td>
                                            @if ($booking->user_id)
                                                <span class="small">
                                                    {{ $booking->user->username }}
                                                </span>
                                                <br>
                                                <a class="fw-bold text--primary" href="tel:{{ $booking->user->email }}">+{{ $booking->user->mobile }}</a>
                                            @else
                                                <span class="small">{{ __(@$booking->guest->name) }}</span>
                                                <br>
                                                <span class="fw-bold">{{ @$booking->guest->email }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($booking->check_in, 'd M, Y') }}
                                            <br>
                                            {{ showDateTime($booking->check_out, 'd M, Y') }}
                                        </td>
                                        <td>{{ showAmount($booking->total_amount) }}</td>
                                        <td>{{ showAmount($booking->paid_amount) }}</td>
                                        @php $due = $booking->total_amount - $booking->paid_amount; @endphp
                                        <td class="@if ($due < 0) text--danger @elseif($due > 0) text--warning @endif">
                                            {{ showAmount($due) }}
                                        </td>
                                        @if (request()->routeIs('owner.booking.all') || request()->routeIs('owner.booking.active'))
                                            <td>@php echo $booking->statusBadge; @endphp </td>
                                        @endif
                                        @can(['owner.booking.details', 'owner.booking.booked.rooms', 'owner.booking.service.details', 'owner.booking.payment', 'owner.booking.key.handover', 'owner.booking.merge', 'owner.booking.cancel', 'owner.booking.extra.charge', 'owner.booking.checkout', 'owner.booking.invoice'])
                                            <td>
                                                <div class="button--group">
                                                    @can('owner.booking.details')
                                                        <a class="btn btn-sm btn-outline--primary" href="{{ route('owner.booking.details', $booking->id) }}">
                                                            <i class="las la-desktop"></i>@lang('Details')
                                                        </a>
                                                    @endcan

                                                    @can(['owner.booking.booked.rooms', 'owner.booking.service.details', 'owner.booking.payment', 'owner.booking.key.handover', 'owner.booking.merge', 'owner.booking.cancel', 'owner.booking.checkout', 'owner.booking.invoice'])
                                                        <button aria-expanded="false" class="btn btn-sm btn-outline--info" data-bs-toggle="dropdown" type="button">
                                                            <i class="las la-ellipsis-v"></i>@lang('More')
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @can('owner.booking.booked.rooms')
                                                                <a class="dropdown-item" href="{{ route('owner.booking.booked.rooms', $booking->id) }}">
                                                                    <i class="las la-desktop"></i> @lang('Booked Rooms')
                                                                </a>
                                                            @endcan
                                                            @can('owner.booking.service.details')
                                                                <a class="dropdown-item" href="{{ route('owner.booking.service.details', $booking->id) }}">
                                                                    <i class="las la-server"></i> @lang('Extra Services')
                                                                </a>
                                                            @endcan
                                                            @can('owner.booking.payment')
                                                                <a class="dropdown-item" href="{{ route('owner.booking.payment', $booking->id) }}">
                                                                    <i class="la la-money-bill"></i> @lang('Payment')
                                                                </a>
                                                            @endcan
                                                            @if ($booking->status == Status::BOOKING_ACTIVE)
                                                                @can('owner.booking.key.handover')
                                                                    @if (now()->format('Y-m-d') >= $booking->check_in && now()->format('Y-m-d') < $booking->check_out && $booking->key_status == Status::DISABLE)
                                                                        <a class="dropdown-item handoverKeyBtn" data-booked_rooms="{{ $booking->activeBookedRooms->unique('room_id') }}" data-id="{{ $booking->id }}" href="javascript:void(0)">
                                                                            <i class="las la-key"></i> @lang('Handover Keys')
                                                                        </a>
                                                                    @endif
                                                                @endcan
                                                                @can('owner.booking.merge')
                                                                    <a class="dropdown-item mergeBookingBtn" data-booking_number="{{ $booking->booking_number }}" data-id="{{ $booking->id }}" href="javascript:void(0)">
                                                                        <i class="las la-object-group"></i> @lang('Merge Booking')
                                                                    </a>
                                                                @endcan
                                                                @can('owner.booking.cancel')
                                                                    <a class="dropdown-item" href="{{ route('owner.booking.cancel', $booking->id) }}">
                                                                        <i class="las la-times-circle"></i> @lang('Cancel Booking')
                                                                    </a>
                                                                @endcan
                                                                @can('owner.booking.checkout')
                                                                    @if (now() >= $booking->check_out)
                                                                        <a class="dropdown-item" href="{{ route('owner.booking.checkout', $booking->id) }}">
                                                                            <i class="la la-sign-out"></i> @lang('Check Out')
                                                                        </a>
                                                                    @endif
                                                                @endcan
                                                            @endif
                                                            @can('owner.booking.invoice')
                                                                <a class="dropdown-item" href="{{ route('owner.booking.invoice', $booking->id) }}" target="_blank"><i class="las la-print"></i> @lang('Print Invoice')</a>
                                                            @endcan
                                                        </div>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($bookings->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($bookings) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('owner.booking.partials.modals')

    <x-confirmation-modal />
@endsection

@can('owner.book.room')
    @push('breadcrumb-plugins')
        <a class="btn btn-sm btn--primary" href="{{ route('owner.book.room') }}">
            <i class="la la-hand-o-right"></i>@lang('Book New')
        </a>
    @endpush
@endcan

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
            "use strict"

            $('.check_in').daterangepicker({
                autoUpdateInput: false,
                singleDatePicker: true,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('.checkout').daterangepicker({
                autoUpdateInput: false,
                singleDatePicker: true,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY'));
            }


            $('.check_in').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));
            $('.checkout').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));


            if ($('.check_in').val()) {
                let dateRange = $('.check_in').val();
                $('.check_in').data('daterangepicker').setStartDate(new Date(dateRange));
                $('.check_in').data('daterangepicker').setEndDate(new Date(dateRange));
            }

            if ($('.checkout').val()) {
                let dateRange = $('.checkout').val();
                $('.checkout').data('daterangepicker').setStartDate(new Date(dateRange));
                $('.checkout').data('daterangepicker').setEndDate(new Date(dateRange));
            }

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .delayed-checkout {
            background-color: #ffefd640;
        }

        .table-responsive {
            min-height: 600px;
            background: transparent
        }

        .card {
            box-shadow: none;
        }
    </style>
@endpush
