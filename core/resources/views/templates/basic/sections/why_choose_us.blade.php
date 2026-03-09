@php
    $frontendWhyChooseUsContent = getContent('why_choose_us.content', true);
    $frontendWhyChooseUsElements = getContent('why_choose_us.element', orderById: true);
@endphp
<div class="why-choose-section py-80">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-6 pe-lg-5">
                <div class="why-choose-thumb">
                    <img src="{{ frontendImage('why_choose_us', $frontendWhyChooseUsContent->data_values->image ?? null, '600x410') }}"
                        alt="image">
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <div class="why-choose-content">
                    <h2 class="why-choose-content__title">
                        {{ __($frontendWhyChooseUsContent->data_values->heading ?? '') }}
                    </h2>
                    <p class="why-choose-content__text">
                        {{ __($frontendWhyChooseUsContent->data_values->subheading ?? '') }}
                    </p>
                    <p class="why-choose-content__desc">
                        {{ __($frontendWhyChooseUsContent->data_values->description ?? '') }}
                    </p>
                    <ul class="service-list">
                        @foreach ($frontendWhyChooseUsElements ?? [] as $frontendWhyChooseUsElement)
                            <li class="service-list__item">
                                <span class="service-list__icon">
                                    @php echo $frontendWhyChooseUsContent->data_values->feature_icon ?? ''; @endphp
                                </span>
                                {{ __($frontendWhyChooseUsElement->data_values->feature ?? '') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
