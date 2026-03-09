@extends('owner.layouts.app')

@section('panel')
    @php $due = $booking->due_amount @endphp
    <div class="row gy-4">
        @if ($due > 0)
            <div class="col-md-12">
                <div class="custom-badge custom-badge--danger">
                    @lang('The guest didn\'t pay the due payment for this booking yet. The checkout process can\'t be completed until the payment is settled. Please receive the due amount.')

                </div>
            </div>
        @endif

        @if ($due < 0)
            <div class="col-md-12">
                <div class="custom-badge custom-badge--danger">
                    @lang('The guest didn\'t receive the refundable amount for this booking yet. The checkout process can\'t be completed until the payment is settled. Please refund the amount.')
                </div>
            </div>
        @endif

        <div class="col-md-6">
            <div class="row gy-4">
                <div class="col-12">
                    @include('owner.booking.partials.guest_info')
                </div>
                <div class="col-12">
                    @include('owner.booking.partials.billing_info')
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row gy-4">
                <div class="col-12">
                    @include('owner.booking.partials.payment_summary')
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center">
                                <span>
                                    @lang('Booking Number'): <span class="fw-500">#{{ $booking->booking_number }}</span>
                                </span>
                                @php
                                    echo $booking->status_badge;
                                    
                                @endphp
                            </div>
                            @can(['owner.booking.invoice', 'owner.booking.checkout', 'owner.booking.payment'])
                                <div class="d-flex flex-wrap justify-content-center gap-3">
                                    @can('owner.booking.invoice')
                                        <a class="btn btn-lg btn--info flex-grow-1" href="{{ route('owner.booking.invoice', $booking->id) }}" target="_blank"><i class="las la-print"></i>@lang('Print Invoice')</a>
                                    @endcan

                                    @can('owner.booking.payment')
                                        <a class="btn btn-lg btn--primary flex-grow-1" href="{{ route('owner.booking.payment', $booking->id) }}"><i class="la la-money-bill"></i>@lang('Go To Payment')</a>
                                    @endcan

                                    @can('owner.booking.checkout')
                                        <button class="btn btn-lg btn--dark flex-grow-1 confirmationBtn" data-action="{{ route('owner.booking.checkout', $booking->id) }}" data-question="@lang('Are you sure, you want to check out this booking?')"><i class="las la-sign-out-alt"></i>@lang('Check Out')</button>
                                    @endcan
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @can('owner.booking.checkout')
        <x-confirmation-modal />
    @endcan
@endsection

@can('owner.booking.all')
    @push('breadcrumb-plugins')
        <x-back route="{{ route('owner.booking.all') }}" />
    @endpush
@endcan

@push('style')
    <style>
        .total {
            color: unset;
        }

        .custom-badge--danger {
            border-left-color: #ea5455 !important;
            color: #ea5455 !important;
        }

        .custom-badge {
            padding: 1.25rem;
            border: 1px solid #e9ecef;
            border-left-width: 0.25rem;
            border-radius: 5px;
            background: #fff;
        }

        .list .list-item {
            border: 1px solid #f1f1f1;
            border-bottom: 0;
            display: flex;
            justify-content: space-between;
            padding: 0.6rem;
        }

        .list .list-item span:first-child {
            font-weight: 500;
            border-radius: 7px 7px 0 0;
        }

        .list .list-item:first-child {
            border-radius: 7px 7px 0 0;
        }

        .list .list-item:last-child {
            border-bottom: 1px solid #f1f1f1;
            border-radius: 0 0 7px 7px;
        }
    </style>
@endpush
