@php
    $frontendTestimonialContent = getContent('testimonial.content', true);
    $frontendTestimonialElements = getContent('testimonial.element');
@endphp
<section class="testimonials py-80 section-bg">
    <div class="container">
        <div class="section-heading">
            <h4 class="section-heading__title">
                {{ __($frontendTestimonialContent?->data_values?->heading ?? '') }}
            </h4>
            <p class="section-heading__desc">
                {{ __($frontendTestimonialContent?->data_values?->subheading ?? '') }}
            </p>
        </div>
        <div class="testimonial-slider">
            @foreach ($frontendTestimonialElements ?? [] as $frontendTestimonialElement)
                <div class="testimonials-card">
                    <div class="testimonial-item">
                        <div class="testimonial-item__content">
                            <div class="testimonial-item__info">
                                <div class="testimonial-item__thumb">
                                    <img src="{{ frontendImage('testimonial', $frontendTestimonialElement->data_values->image ?? null, '60x60') }}"
                                         class="fit-image" alt="testimonial image">
                                </div>
                                <div class="testimonial-item__details">
                                    <h6 class="testimonial-item__name">
                                        {{ __($frontendTestimonialElement->data_values->name ?? '') }}
                                    </h6>
                                    <span class="testimonial-item__designation">
                                        {{ __($frontendTestimonialElement->data_values->city ?? '') }},
                                        {{ __($frontendTestimonialElement->data_values->country ?? '') }}
                                    </span>
                                </div>
                            </div>
                            <div class="testimonial-item__rating">
                                <ul class="rating-list">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $frontendTestimonialElement->data_values->rating)
                                            <li class="rating-list__item">
                                                <i class="las la-star"></i>
                                            </li>
                                        @elseif($i > $frontendTestimonialElement->data_values->rating)
                                            <li class="rating-list__item">
                                                <i class="lar la-star"></i>
                                            </li>
                                        @endif
                                    @endfor
                                </ul>
                                <p class="text">
                                    {{ showDateTime($frontendTestimonialElement->created_at ?? now(), 'F Y') }}
                                </p>
                            </div>
                        </div>
                        <p class="testimonial-item__desc">
                            {{ __($frontendTestimonialElement->data_values->review ?? '') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('script')
    <script>
        (function($) {
            'use strict';

            $(".testimonial-slider").slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                Infinity: true,
                centerMode: true,
                autoplay: true,
                autoplaySpeed: 2000,
                speed: 1500,
                dots: true,
                pauseOnHover: true,
                arrows: false,
                prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-long-arrow-alt-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fas fa-long-arrow-alt-right"></i></button>',
                responsive: [{
                        breakpoint: 1199,
                        settings: {
                            arrows: false,
                            slidesToShow: 2,
                            dots: true,
                        },
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            arrows: false,
                            slidesToShow: 2,
                        },
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            arrows: false,
                            slidesToShow: 1,
                        },
                    },
                ],
            });
        })(jQuery);
    </script>
@endpush
