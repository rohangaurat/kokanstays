@php $frontendAboutUsContent = getContent('about_us.content', true); @endphp

<div class="about-section py-80">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6 pe-lg-5">
                <div class="about-wrapper">
                    <x-shape shapeClass="shape-one" fileName="about-2" />
                    <x-shape shapeClass="shape-two" fileName="about-1" />
                    <div class="about-thumb">
                        <img src="{{ frontendImage('about_us', $frontendAboutUsContent?->data_values?->image ?? null, '600x455') }}"
                             alt="about-image">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <div class="about-content">
                    <h2 class="about-content__title">
                        {{ __($frontendAboutUsContent?->data_values?->heading ?? '') }}
                    </h2>
                    <p class="about-content__text">
                        {{ __($frontendAboutUsContent?->data_values?->short_description ?? '') }}
                    </p>
                    <p class="about-content__desc">
                        {{ __($frontendAboutUsContent?->data_values?->description ?? '') }}
                    </p>
                    <div class="about-content__btn">
                        <a href="{{ url($frontendAboutUsContent?->data_values?->button_url ?? '#') }}"
                           class="btn btn--base btn--lg">
                            {{ __($frontendAboutUsContent?->data_values?->button_text ?? '') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
