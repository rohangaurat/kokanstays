<div class="row gy-4">
    @foreach ($bookings as $booking)
        <div class="col-xxl-3 col-xl-4 col-md-6">
            <div class="widget-two box--shadow2 b-radius--5 {{ @$class }}">
                <div class="widget-two__content d-flex align-items-end justify-content-between flex-wrap">
                    @if ($booking->user_id)
                        <h3>
                            <span class="f-size--18 text-center text--dark">
                                {{ __($booking->user->fullname) }}
                            </span>
                        </h3>
                    @else
                        <h3 class="f-size--18 text--dark"><i class="las la-user-circle"></i> {{ @$booking->guest->name }}</h3>
                    @endif

                    <div class="d-flex flex-column fw-bold w-100">
                        <p class="text--muted text--small">@lang('Mobile'):
                            @if ($booking->user_id)
                                +{{ $booking->user->mobile }}
                            @else
                                +{{ @$booking->guest->mobile }}
                            @endif
                        </p>
                        <p class="text--muted text--small">@lang('Booking No.'):
                            @can('owner.booking.all')
                                <a class="text--small fw-bold" href="{{ route('owner.booking.details', $booking->id) }}">{{ $booking->booking_number }}</a>
                            @else
                                <span class="text--small fw-bold">{{ $booking->booking_number }}</span>
                            @endcan
                        </p>
                        <p class="text--muted text--small">@lang('Total Rooms'): {{ $booking->total_room }}</p>

                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
