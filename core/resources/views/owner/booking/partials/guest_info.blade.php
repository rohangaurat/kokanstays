<div class="card">
    <div class="card-body">
        <h5 class="card-title">@lang('Guest Info')</h5>
        <div class="list">
            <div class="list-item">
                <span>@lang('Name')</span>
                <span>
                    @if ($booking->user_id)
                        {{ __($booking->user->fullname) }}
                    @else
                        {{ $booking->guest->name }}
                    @endif
                </span>
            </div>
            <div class="list-item">
                <span>@lang('Email')</span>
                <span>
                    @if ($booking->user_id)
                        {{ $booking->user->email }}
                    @else
                        {{ $booking->guest->email }}
                    @endif
                </span>
            </div>
            <div class="list-item">
                <span>@lang('Phone')</span>
                <span>
                    @if ($booking->user_id)
                        +{{ $booking->user->mobile }}
                    @else
                        +{{ $booking->guest->mobile }}
                    @endif
                </span>
            </div>
            <div class="list-item">
                <span>@lang('Address')</span>
                <span>
                    @if ($booking->user_id)
                        {{ $booking->user->address->address ?? 'N/A' }}
                    @else
                        {{ $booking->guest->address ?? 'N/A' }}
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>
