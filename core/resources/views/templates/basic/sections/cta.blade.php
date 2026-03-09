@php $frontendCtaContent = getContent('cta.content', true); @endphp
<div class="cta-section pt-80">
    <div class="container">
        @php
        $backgroundImage = frontendImage('cta', $frontendCtaContent?->data_values?->image ?? null, '1285x235'); @endphp
        <div class="cta-wrapper bg-img" data-background-image="{{ $backgroundImage }}"
             style="background: url('{{ $backgroundImage }}');">
            <div class="cta-content">
                <h3 class="cta-content__title">
                    {{ __($frontendCtaContent?->data_values?->heading ?? '') }}
                </h3>
                <h5 class="cta-content__offer"> {{ __($frontendCtaContent?->data_values?->subheading ?? '') }}</h5>
                <div class="cta-content__btn">
                    <a href="{{ $frontendCtaContent?->data_values?->button_url ?? '#' }}" class="btn btn--white">
                        {{ __($frontendCtaContent?->data_values?->button_text ?? '') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
