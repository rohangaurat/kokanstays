@php
    $frontendAppDownloadContent = getContent('app_download.content', true);
    $frontendAppDownloadElements = getContent('app_download.element');
@endphp
<section class="app-section">
    <div class="app-section__shape">
        <img src="{{ frontendImage('app_download', $frontendAppDownloadContent?->data_values?->image_two ?? null, '485x480') }}"
             alt="image">
    </div>
    <x-shape shapeClass="app-section__shape-two" fileName="app-1" />
    <div class="container">
        <div class="row gy-5 align-items-center justify-content-center justify-content-md-between">
            <div class="col-lg-5 col-md-6">
                <div class="app-content">
                    <h2 class="app-content__title">
                        {{ __($frontendAppDownloadContent?->data_values?->heading ?? '') }}
                    </h2>
                    <p class="app-content__desc">
                        {{ __($frontendAppDownloadContent?->data_values?->subheading ?? '') }}
                    </p>
                    <div class="download-item">
                        <p class="download-item__text">
                            {{ __($frontendAppDownloadContent?->data_values?->title ?? '') }}
                        </p>
                        <div class="flex-align gap-2">
                            @foreach ($frontendAppDownloadElements ?? [] as $frontendAppDownloadElement)
                                <a href="{{ url($frontendAppDownloadElement->data_values->url ?? '#') }}"
                                   class="download-item__link">
                                    <img src="{{ frontendImage('app_download', $frontendAppDownloadElement->data_values->image, '145x45') }}"
                                         alt="download image">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-6 ps-lg-5">
                <div class="app-thumb">
                    <img src="{{ frontendImage('app_download', $frontendAppDownloadContent?->data_values?->image_one ?? null, '580x580') }}"
                         alt="image">
                </div>
            </div>
        </div>
    </div>
</section>
