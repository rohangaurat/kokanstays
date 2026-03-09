<div class="review-card">
    <h6 class="review-card__title m-0 mb-2 skeleton">{{ __($roomType->name) }}</h6>
    <span class="review-card__info mb-2 skeleton">
        @php $beds = $roomType->bedCount(); @endphp
        @if (!blank($beds))
            <i class="las la-bed"></i>
            @foreach ($beds as $bed => $count)
                {{ $count . ' ' . __($bed) }} {{ $loop->last ? '' : '|' }}
            @endforeach
        @endif
    </span>
    <div class="review-card__content">
        @if (!blank($roomType->facilities))
            @foreach ($roomType->facilities as $facility)
                <p class="review-card__desc skeleton">{{ __($facility->name) }}</p>
            @endforeach
        @endif
    </div>
</div>
