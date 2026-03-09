@extends('owner.layouts.app')
@section('panel')
    <div class="row gy-4">
        @if (!blank($bookings))
            <div class="col-12">
                <h6 class="mb-3 text--danger border-bottom pb-1"> <i class="las la-info-circle"></i> {{ __($alertText) }}</h6>
                @include('owner.booking.partials.booking_info_cards', ['bookings' => $bookings, 'class' => 'warning-light'])
            </div>
        @endif

        @if (blank($bookings))
            <div class="col-12">
                <div class="card empty-card">
                    <div class="card-body">
                        <div class="text-center message">
                            <i class="las la-file la-3x"></i>
                            <h4>{{ __($emptyMessage) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('style')
    <style>
        .warning-light {
            background-color: #ff9f4336;
        }

        .empty-card h4 {
            color: #a5a5a5;
        }

        .message {
            font-size: 38px;
            color: #e9e9e9;
        }
    </style>
@endpush
