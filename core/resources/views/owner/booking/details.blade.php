@extends('owner.layouts.app')
@section('panel')
    @php
        $totalFare = $booking->bookedRooms->sum('fare');
        $totalTaxCharge = $booking->bookedRooms->sum('tax_charge');
        $canceledFare = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('fare');
        $canceledTaxCharge = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('tax_charge');
        $due = $booking->total_amount - $booking->paid_amount;
    @endphp

    <div class="row gy-4">
        <div class="col-md-4 ">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <small class="fw-500"> <i class="las la-user-edit"></i> @lang('Guest Type')</small><br>
                            @if ($booking->user_id)
                                <span class="d-bock">@lang('Registered Guest')</span>
                            @else
                                <span class="d-bock">@lang('Walk In Guest')</span>
                            @endif
                        </div>

                        <div>

                            <small class="fw-500"> <i class="la la-user"></i> @lang('Name')</small><br>
                            @if ($booking->user_id)
                                <span class="d-block">{{ $booking->user->fullname }}</span>
                            @else
                                <span class="d-block">{{ @$booking->guest->name }}</span>
                            @endif
                        </div>

                        <div>
                            <small class="fw-500"><i class="la la-envelope"></i> @lang('Email')</small><br>
                            @if ($booking->user_id)
                                <span class="d-block">{{ $booking->user->email }}</span>
                            @else
                                <span class="d-block">{{ @$booking->guest->email }}</span>
                            @endif
                        </div>

                        <div>
                            <small class="fw-500"><i class="la la-mobile"></i> @lang('Mobile')</small>

                            <span class="d-block">
                                @if ($booking->user_id)
                                    {{ $booking->user->mobile }}
                                @else
                                    {{ @$booking->guest->mobile }}
                                @endif
                            </span>
                        </div>

                        @php
                            $address = $booking->user_id ? $booking->user->address : @$booking->guest->address;
                        @endphp

                        <div>
                            <small class="fw-500"><i class="la la-map"></i> @lang('Address')</small> <br>
                            <span>
                                {{ $address ?? __('N/A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card position-relative">
                <div class="card-body">
                    <div class="custom-badge position-absolute">
                        @php
                            echo $booking->status_badge;
                        @endphp
                    </div>

                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <small class="fw-500">@lang('Booking No.')</small> <br>
                                <span>#{{ $booking->booking_number }}</span>
                            </div>

                            <div>
                                <small class="fw-500">@lang('Total Room')</small> <br>
                                <span>{{ $booking->bookedRooms->count() }}</span>
                            </div>

                            <div>
                                <small class="fw-500">@lang('Total Charge')</small> <br>
                                <span>{{ showAmount($booking->total_amount) }}</span>
                            </div>

                            <div>
                                <small class="fw-500">@lang('Paid Amount')</small> <br>
                                <span>{{ showAmount($booking->paid_amount) }}</span>
                            </div>

                            <div>
                                @if ($due < 0)
                                    <small class="fw-500">@lang('Refundable') </small> <br>
                                    <span class="text--warning">{{ showAmount(abs($due)) }}</span>
                                @else
                                    <small class="fw-500">@lang('Receivable from User')</small><br>
                                    <span class="@if ($due > 0) text--danger @else text--success @endif">
                                        {{ showAmount(abs($due)) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <div>
                                <small class="fw-500">@lang('Booked At')</small> <br>
                                <small> <em
                                        class="text-muted">{{ showDateTime($booking->created_at, 'd M, Y h:i A') }}</em></small>
                            </div>

                            <div>
                                <small class="fw-500">@lang('Check-In')</small> <br>
                                <small><em>{{ showDateTime($booking->check_in, 'd M, Y') }}</em></small>
                            </div>

                            <div>
                                <small class="fw-500">@lang('Checkout')</small> <br>
                                <small><em>{{ showDateTime($booking->check_out, 'd M, Y') }}</em></small>
                            </div>

                            <div>
                                <small class="fw-500">@lang('Checked-In At')</small> <br>
                                <small> <em class="text-muted">
                                        @if ($booking->checked_in_at)
                                            {{ showDateTime($booking->checked_in_at, 'd M, Y h:i A') }}
                                        @else
                                            @lang('N/A')
                                        @endif
                                    </em>
                                </small>
                            </div>
                            <div>
                                <small class="fw-500">@lang('Checked Out At')</small> <br>
                                <small> <em class="text-muted">
                                        @if ($booking->checked_out_at)
                                            {{ showDateTime($booking->checked_out_at, 'd M, Y h:i A') }}
                                        @else
                                            @lang('N/A')
                                        @endif
                                    </em>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="accordion d-flex flex-column gap-4">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="bookedRoomsHeading">
                        <button aria-controls="bookedRooms" aria-expanded="true" class="accordion-button"
                            data-bs-target="#bookedRooms" data-bs-toggle="collapse"
                            type="button">@lang('Booked Rooms')</button>
                    </h2>
                    <div aria-labelledby="bookedRoomsHeading" class="accordion-collapse collapse show" data-bs-parent="#s"
                        id="bookedRooms">
                        <div class="accordion-body p-0">
                            <div class="table-responsive--sm">
                                <table class="custom--table table table-striped">
                                    <thead>
                                        <tr>
                                            <th>@lang('Booked For')</th>
                                            <th>@lang('Room Type')</th>
                                            <th>@lang('Room No.')</th>
                                            <th class="text-end">@lang('Fare') / @lang('Night')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($booking->bookedRooms->groupBy('booked_for') as $bookedRoom)
                                            @foreach ($bookedRoom as $booked)
                                                <tr>
                                                    @if ($loop->first)
                                                        <td class="bg--date text-start" rowspan="{{ count($bookedRoom) }}">
                                                            {{ showDateTime($booked->booked_for, 'd M, Y') }}
                                                        </td>
                                                    @endif
                                                    <td class="text-center" data-label="@lang('Room Type')">
                                                        {{ __($booked->room->roomType->name) }}
                                                    </td>
                                                    <td data-label="@lang('Room No.')">
                                                        {{ __($booked->room->room_number) }}
                                                        @if ($booked->status == Status::ROOM_CANCELED)
                                                            <span class="text--danger text-sm">(@lang('Canceled'))</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end" data-label="@lang('Fare')">
                                                        {{ showAmount($booked->fare) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        <tr>
                                            <td class="text-end" colspan="3">
                                                <span class="fw-bold">@lang('Total Fare')</span>
                                            </td>
                                            <td class="fw-bold text-end">
                                                {{ showAmount($totalFare) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="extraServiceHeading">
                        <button aria-controls="extraServices" aria-expanded="false" class="accordion-button"
                            data-bs-target="#extraServices" data-bs-toggle="collapse" type="button">
                            @lang('Extra Services')
                        </button>
                    </h2>
                    <div aria-labelledby="extraServiceHeading" class="accordion-collapse collapse show" data-bs-parent="#s"
                        id="extraServices">
                        <div class="accordion-body p-0">
                            @if ($booking->usedExtraService->count())
                                <div class="table-responsive--sm">
                                    <table class="custom--table head--base table table-striped">
                                        <thead>
                                            <tr>
                                                <th>@lang('Date')</th>
                                                <th>@lang('Room No.')</th>
                                                <th>@lang('Service')</th>
                                                <th>@lang('Total')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($booking->usedExtraService->groupBy('service_date') as $services)
                                                @foreach ($services as $service)
                                                    <tr>
                                                        @if ($loop->first)
                                                            <td class="bg--date text-start" data-label="@lang('Date')"
                                                                rowspan="{{ count($services) }}">
                                                                {{ showDateTime($service->service_date, 'd M, Y') }}
                                                            </td>
                                                        @endif
                                                        <td data-label="@lang('Room No.')">
                                                            <span
                                                                class="fw-bold">{{ __($service->room->room_number) }}</span>
                                                        </td>
                                                        <td data-label="@lang('Service')">
                                                            <span class="fw-bold">
                                                                {{ __($service->extraService->name) }}
                                                            </span>
                                                            <br>
                                                            {{ showAmount($service->unit_price) }} x {{ $service->qty }}
                                                        </td>
                                                        <td data-label="@lang('Total')">
                                                            <span class="fw-bold text-end">
                                                                {{ showAmount($service->total_amount) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            <tr>
                                                <td class="text-end" colspan="3">
                                                    <span class="fw-bold">@lang('Total')</span>
                                                </td>
                                                <td class="fw-bold text-end">{{ showAmount($booking->service_cost) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center">
                                    <h6 class="p-3">@lang('No extra service used')</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @php
                    $receivedPayments = $booking->payments->where('type', 'BOOKING_PAYMENT_RECEIVED');
                    $returnedPayments = $booking->payments->where('type', 'BOOKING_PAYMENT_RETURNED');
                @endphp

                <div class="accordion-item">
                    <h2 class="accordion-header" id="paymentReceived">
                        <button aria-controls="paymentsReceived" aria-expanded="false" class="accordion-button"
                            data-bs-target="#paymentsReceived" data-bs-toggle="collapse" type="button">
                            @lang('Payments Received')
                        </button>
                    </h2>
                    <div aria-labelledby="paymentReceived" class="accordion-collapse collapse show" data-bs-parent="#s"
                        id="paymentsReceived">
                        <div class="accordion-body p-0">
                            <div class="table-responsive--sm">
                                <table class="custom--table head--base table table-striped">
                                    <thead>
                                        <tr>
                                            <th>@lang('Time')</th>
                                            <th>@lang('Payment Type')</th>
                                            <th>@lang('Amount')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($receivedPayments as $payment)
                                            <tr>
                                                <td class="text-start">
                                                    {{ __(showDateTime($payment->created_at, 'd M, Y H:i A')) }}
                                                </td>
                                                <td>{{ __($payment->payment_system) }}</td>
                                                <td>{{ showAmount($payment->amount) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td class="text-end fw-bold" colspan="2">
                                                @lang('Total')
                                            </td>
                                            <td class="fw-bold">{{ showAmount($receivedPayments->sum('amount')) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="paymentReturned">
                        <button aria-controls="paymentsReturned" aria-expanded="false" class="accordion-button"
                            data-bs-target="#paymentsReturned" data-bs-toggle="collapse" type="button">
                            @lang('Payments Returned')
                        </button>
                    </h2>
                    <div aria-labelledby="paymentReturned" class="accordion-collapse collapse show" data-bs-parent="#s"
                        id="paymentsReturned">
                        <div class="accordion-body p-0">
                            <table class="custom--table head--base table table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('Time')</th>
                                        <th>@lang('Payment Type')</th>
                                        <th>@lang('Amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($returnedPayments as $payment)
                                        <tr>
                                            <td class="text-start">
                                                {{ __(showDateTime($payment->created_at, 'd M, Y H:i A')) }}
                                            </td>
                                            <td>@lang('Cash Payment')</td>
                                            <td>{{ showAmount($payment->amount) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="text-end" colspan="2">
                                            <span class="fw-bold">@lang('Total')</span>
                                        </td>
                                        <td class="fw-bold">{{ showAmount($returnedPayments->sum('amount')) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="summaryHeading">
                        <button aria-controls="summaryBody" aria-expanded="false" class="accordion-button"
                            data-bs-target="#summaryBody" data-bs-toggle="collapse" type="button">
                            @lang('Payment Info')
                        </button>
                    </h2>
                    <div aria-labelledby="summaryHeading" class="accordion-collapse collapse show" data-bs-parent="#s"
                        id="summaryBody">
                        <div class="accordion-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    <span>@lang('Total Fare')</span>
                                    <span>{{ showAmount($totalFare) }}</span>
                                </li>
                                @if ($booking->total_discount > 0)
                                    <li class="d-flex justify-content-between list-group-item align-items-start">
                                        <span>@lang('Discount')</span>
                                        <span> -{{ showAmount($booking->total_discount) }}</span>
                                    </li>

                                    <li class="d-flex justify-content-between list-group-item align-items-start fw-bold">
                                        <span>@lang('Subtotal')</span>
                                        <span> ={{ showAmount($totalFare - $booking->total_discount) }}</span>
                                    </li>
                                @endif
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    <span>{{ __(hotelSetting('tax_name')) }} @lang('Charge')
                                        <small>({{ getAmount($booking->taxPercentage()) }}%)</small></span>
                                    <span>{{ showAmount($totalTaxCharge) }}</span>
                                </li>
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    <span>@lang('Canceled Fare')</span>
                                    <span> -{{ showAmount($canceledFare) }}</span>
                                </li>
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    <span>@lang('Canceled') {{ __(hotelSetting('tax_name')) }} @lang('Charge')</span>
                                    <span> -{{ showAmount($canceledTaxCharge) }}</span>
                                </li>
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    <span>@lang('Extra Service Charge')</span>
                                    <span>{{ showAmount($booking->service_cost) }}</span>
                                </li>
                                @if ($booking->extraCharge() > 0)
                                    <li class="d-flex justify-content-between list-group-item align-items-start">
                                        <span>@lang('Other Charges')</span>
                                        <span>{{ showAmount($booking->extraCharge()) }}</span>
                                    </li>
                                @endif
                                @if ($booking->cancellation_fee > 0)
                                    <li class="d-flex justify-content-between list-group-item align-items-start">
                                        <span>@lang('Cancellation Fee')</span>
                                        <span>{{ showAmount($booking->cancellation_fee) }}</span>
                                    </li>
                                @endif
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    <span class="fw-bold">@lang('Total Amount')</span>
                                    <span class="fw-bold"> = {{ showAmount($booking->total_amount) }}</span>
                                </li>
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    <span>@lang('Payment Received')</span>
                                    <span>{{ showAmount($receivedPayments->sum('amount')) }}</span>
                                </li>
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    <span>@lang('Refunded')</span>
                                    <span>{{ showAmount($returnedPayments->sum('amount')) }}</span>
                                </li>
                                @php $due = $booking->due_amount; @endphp
                                <li class="d-flex justify-content-between list-group-item align-items-start">
                                    @if ($due < 0)
                                        <span class="text--warning fw-bold">@lang('Refundable') </span>
                                        <span class="text--warning fw-bold">{{ showAmount(abs($due)) }}</span>
                                    @else
                                        <span
                                            class="@if ($due > 0) text--danger @else text--success @endif fw-bold">
                                            @lang('Receivable from User')
                                        </span>
                                        <span
                                            class="@if ($due > 0) text--danger @else text--success @endif fw-bold">
                                            {{ showAmount(abs($due)) }}
                                        </span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('owner.booking.partials.modals')
    @endsection

    @can(['owner.booking.details', 'owner.booking.booked.rooms', 'owner.booking.service.details',
        'owner.booking.payment', 'owner.booking.key.handover', 'owner.booking.merge', 'owner.booking.cancel',
        'owner.booking.extra.charge', 'owner.booking.checkout', 'owner.booking.invoice', 'owner.booking.all'])

        @push('breadcrumb-plugins')
            @can('owner.booking.all')
                <a class="btn btn-sm btn--primary" href="{{ route('owner.booking.all') }}">
                    <i class="la la-list"></i>@lang('All Bookings')
                </a>
            @endcan

            @can(['owner.booking.booked.rooms', 'owner.booking.service.details', 'owner.booking.payment',
                'owner.booking.key.handover', 'owner.booking.merge', 'owner.booking.cancel', 'owner.booking.checkout',
                'owner.booking.invoice'])
                <button aria-expanded="false" class="btn btn-sm btn--info dropdown-toggle" data-bs-toggle="dropdown"
                    type="button">
                    <i class="la la-ellipsis-v"></i>@lang('More')
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
                            @if (now()->format('Y-m-d') >= $booking->check_in &&
                                    now()->format('Y-m-d') < $booking->check_out &&
                                    $booking->key_status == Status::DISABLE)
                                <a class="dropdown-item handoverKeyBtn"
                                    data-booked_rooms="{{ $booking->activeBookedRooms->unique('room_id') }}"
                                    data-id="{{ $booking->id }}" href="javascript:void(0)">
                                    <i class="las la-key"></i> @lang('Handover Keys')
                                </a>
                            @endif
                        @endcan

                        @can('owner.booking.merge')
                            <a class="dropdown-item mergeBookingBtn" data-booking_number="{{ $booking->booking_number }}"
                                data-id="{{ $booking->id }}" href="javascript:void(0)">
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
                        <a class="dropdown-item" href="{{ route('owner.booking.invoice', $booking->id) }}" target="_blank"><i
                                class="las la-print"></i> @lang('Print Invoice')</a>
                    @endcan
                </div>
            @endcan
        @endpush
    @endcan

    @push('style')
        <style>
            .custom-badge {
                top: -15px;
                left: calc(50% - 75px)
            }

            .custom-badge .badge {
                width: 150px;
                height: 30px;
                line-height: 24px;
                font-size: 1rem !important;
                font-weight: 500;
            }

            .table-striped>tbody>tr:nth-of-type(odd)>* {
                --bs-table-accent-bg: rgb(255 255 255 / 37%);
            }

            .custom--table thead th {
                background-color: #d9d9d9;
            }

            .custom--table th,
            .custom--table td {
                border: 1px solid #e8e8e8;
            }

            .custom--table {
                border: 1px solid #e8e8e8;
                border-collapse: collapse;
            }

            .custom--table tbody td:first-child {
                text-align: center;
            }

            .custom--table tbody td,
            .custom--table thead th {
                color: #5b6e88 !important;
            }

            @media (min-width: 768px) {
                .custom--table tbody td {
                    padding: 0.5rem 1rem !important;
                }

                .custom--table thead th {
                    padding: 1rem !important;
                }
            }

            .accordion-button:focus {
                box-shadow: none;
            }

            .accordion-button:not(.collapsed) {
                color: #fff;
                background-color: #071251;
                font-weight: bold;
            }

            .list-group-item:nth-of-type(odd) {
                background-color: #f9f9f9f2;
            }

            .accordion-button:not(.collapsed)::after {
                filter: brightness(0) invert(1);
            }

            table thead th:first-child {
                border-radius: 0;
            }

            .accordion-item:first-of-type .accordion-button {
                border-radius: unset;
            }

            .accordion-item:has(table) {
                border: 0;
            }
        </style>
    @endpush
