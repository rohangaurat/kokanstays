@extends('Template::layouts.master')
@section('content')
    @if (!blank($bookingRequests))
        <div class="booking-main-wrapper">
            <div class="row gy-4">
                @foreach ($bookingRequests as $booking)
                    @include('Template::partials.booking_card', [
                        'booking' => $booking,
                        'detailsRoute' => 'user.booking.request.details',
                        'badge' => $booking->customStatusBadge,
                        'totalGuest' => $booking->total_guest ?? 0,
                        'bookingId' => $booking->booking_number ?? null,
                        'totalAmount' => $booking->total_amount,
                        'bookAgain' => $booking->check_in < now() ? true : false,
                        'dueAmount' => 0,
                    ])
                @endforeach
            </div>
            @if ($bookingRequests->hasPages())
                <div class="mt-4">{{ paginateLinks($bookingRequests) }}</div>
            @endif
        </div>
    @else
        <div class="card custom--card">
            <div class="card-body">
                @include('Template::partials.empty_list', ['message' => 'No booking request found.'])
            </div>
        </div>
    @endif

    @include('Template::partials.confirmationModal')
@endsection

@push('style')
    <style>
        .booking-card.card-three .booking-card__right .price {
            font-size: 16px;
        }

        .booking-card.card-three .booking-card__content-inner {
            align-items: flex-start;
        }

        .booking-card.card-three .room-info-list__text:first-child::after {
            display: none;
        }
    </style>
@endpush
