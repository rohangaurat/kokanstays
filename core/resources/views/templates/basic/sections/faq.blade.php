@php
    $frontendFaqContent = getContent('faq.content', true);
    $frontendFaqElements = getContent('faq.element', orderById: true);
    $midPoint = ceil($frontendFaqElements->count() / 2);
@endphp
<section class="faq-section py-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading">
                    <h4 class="section-heading__title">
                        {{ __($frontendFaqContent?->data_values?->heading ?? '') }}
                    </h4>
                    <p class="section-heading__desc">
                        {{ __($frontendFaqContent?->data_values?->subheading ?? '') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="accordion custom--accordion" id="accordionExample">
            <div class="row gy-4 justify-content-center">
                <div class="col-lg-6">
                    @for ($i = 0; $i < $midPoint; $i++)
                        <div class="accordion-item">
                            <h6 class="accordion-header" id="heading_{{ $i }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse_{{ $i }}" aria-expanded="false"
                                        aria-controls="collapse_{{ $i }}">
                                    {{ __($frontendFaqElements[$i]?->data_values?->question ?? '') }}
                                </button>
                            </h6>
                            <div id="collapse_{{ $i }}" class="accordion-collapse collapse"
                                 aria-labelledby="heading_{{ $i }}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    {{ __($frontendFaqElements[$i]?->data_values?->answer ?? '') }}
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="col-lg-6">
                    @for ($i = $midPoint; $i < $frontendFaqElements->count(); $i++)
                        <div class="accordion-item">
                            <h6 class="accordion-header" id="heading_{{ $i }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse_{{ $i }}" aria-expanded="false"
                                        aria-controls="collapse_{{ $i }}">
                                    {{ __($frontendFaqElements[$i]?->data_values?->question ?? '') }}
                                </button>
                            </h6>
                            <div id="collapse_{{ $i }}" class="accordion-collapse collapse"
                                 aria-labelledby="heading_{{ $i }}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    {{ __($frontendFaqElements[$i]?->data_values?->answer ?? '') }}
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>
