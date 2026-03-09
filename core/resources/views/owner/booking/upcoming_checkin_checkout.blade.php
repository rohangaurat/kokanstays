@extends('owner.layouts.app')
@section('panel')
    <div class="row gy-4">
        @if (!blank($bookings))
            <div class="col-12">
                <div class="row gy-4">
                    @foreach ($bookings as $date => $groupedBookings)
                        <div class="col-12">
                            <h4 class="mb-2">{{ showDateTime($date, 'd M, Y') }}</h4>
                            @include('owner.booking.partials.booking_info_cards', ['bookings' => $groupedBookings, 'class' => 'bg--white'])
                        </div>
                    @endforeach
                </div>
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
        .empty-card h4 {
            color: #a5a5a5;
        }

        .message {
            font-size: 38px;
            color: #e9e9e9;
        }
    </style>
@endpush
