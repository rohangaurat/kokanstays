@props([
    'rating' => 0,
    'showRating' => false,
    'reviewCount' => 0,
])
@php
    $fullStars = floor($rating);
    $fractionalPart = $rating - $fullStars;
    $halfStar = 0;
    if ($fractionalPart >= 0.25 && $fractionalPart <= 0.75) {
        $halfStar = 1;
    }
    $emptyStars = 5 - $fullStars - $halfStar;
@endphp

<ul class="rating-list">
    @for ($i = 0; $i < $fullStars; $i++)
        <li class="rating-list__item">
            <i class="fas fa-star"></i>
        </li>
    @endfor
    @if ($halfStar)
        <li class="rating-list__item">
            <i class="far fa-star-half-stroke"></i>
        </li>
    @endif
    @for ($i = 0; $i < $emptyStars; $i++)
        <li class="rating-list__item">
            <i class="far fa-star"></i>
        </li>
    @endfor
    @if ($showRating)
        <li class="rating-list__number">{{ $rating }}</li>
        <li class="rating-list__text"> ({{ $reviewCount }} @lang('Reviews')) </li>
    @endif
</ul>
