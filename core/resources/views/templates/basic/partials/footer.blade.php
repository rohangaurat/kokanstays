@php
    $frontendFooterContent = getContent('footer.content', true);
    $frontendSocialIconsElements = getContent('social_icon.element', orderById: true);
    $frontendFooterContactContent = getContent('contact_us.content', true);
    $frontendAppDownloadContent = getContent('app_download.content', true);
    $frontendAppDownloadElements = getContent('app_download.element');
    $footerPopularCities = App\Models\City::active()
        ->popular()
        ->withCount([
            'hotelSettings' => function ($hotelSetting) {
                $hotelSetting->whereHas('owner', function ($owner) {
                    $owner
                        ->where('status', Status::USER_ACTIVE)
                        ->whereDate('owners.expire_at', '>=', now())
                        ->whereHas('roomTypes', function ($roomTypes) {
                            $roomTypes->where('status', Status::ROOM_TYPE_ACTIVE)->whereHas('rooms', function ($rooms) {
                                $rooms->where('status', Status::ROOM_ACTIVE);
                            });
                        });
                });
            },
        ])
        ->orderByDesc('hotel_settings_count')
        ->limit(4)
        ->get();
@endphp
<footer class="footer-area">
    <div class="footer-area__bg bg-img"
         data-background-image="{{ frontendImage('footer', $frontendFooterContent->data_values->image ?? null, '1950x450') }}">
    </div>
    <div class="py-80">
        <div class="container">
            <div class="row justify-content-center gy-5">
                <div class="col-xl-3 col-sm-6 col-xsm-6 ">
                    <div class="footer-item">
                        <div class="footer-item__logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ siteLogo() }}" alt="logo">
                            </a>
                        </div>
                        <p class="footer-item__desc">
                            {{ __($frontendFooterContent->data_values->description ?? '') }}
                        </p>

                        <div class="download-item">
                            <p class="download-item__text">
                                {{ __($frontendAppDownloadContent->data_values->title ?? '') }}
                            </p>
                            @if (!blank($frontendAppDownloadElements))
                                <div class="flex-align gap-2">
                                    @foreach ($frontendAppDownloadElements as $frontendAppDownloadElement)
                                        <a href="{{ $frontendAppDownloadElement->data_values->url }}"
                                           class="download-item__link" target="_blank">
                                            <img src="{{ frontendImage('app_download', $frontendAppDownloadElement->data_values->image ?? null, '145x45') }}"
                                                 alt="app download image">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6 ps-lg-5">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Policy Links')</h6>
                        <ul class="footer-menu">
                            @php $policyPages = getContent('policy_pages.element', false, orderById: true); @endphp
                            @foreach ($policyPages as $policy)
                                <li class="footer-menu__item">
                                    <a href="{{ route('policy.pages', $policy->slug) }}" class="footer-menu__link">
                                        {{ __($policy->data_values->title) }}
                                    </a>
                                </li>
                            @endforeach
                            <li class="footer-menu__item">
                                <a href="{{ route('cookie.policy') }}" class="footer-menu__link">@lang('Cookie Policy')</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Useful Links')</h6>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a href="{{ route('about') }}" class="footer-menu__link">
                                    @lang('About')
                                </a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="{{ route('contact') }}" class="footer-menu__link">
                                    @lang('Contact')
                                </a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="{{ route('owner.login') }}" class="footer-menu__link">
                                    @lang('Vendor Login')
                                </a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="{{ route('owner.register') }}" class="footer-menu__link">
                                    @lang('Vendor Registration')
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Contact With Us')</h6>
                        <ul class="footer-contact-menu">
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    @php echo $frontendFooterContactContent->data_values->address_icon ?? ''; @endphp
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p>{{ __($frontendFooterContactContent->data_values->address ?? '') }}</p>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    @php echo $frontendFooterContactContent->data_values->phone_icon ?? ''; @endphp
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <a href="tel:{{ __($frontendFooterContactContent->data_values->phone ?? '') }}">
                                        {{ __($frontendFooterContactContent->data_values->phone ?? '') }}
                                    </a>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    @php echo $frontendFooterContactContent->data_values->email_icon ?? ''; @endphp
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <a href="mailto:{{ __($frontendFooterContactContent->data_values->email ?? '') }}">
                                        {{ __($frontendFooterContactContent->data_values->email ?? '') }}
                                    </a>
                                </div>
                            </li>
                        </ul>
                        @if (!blank($frontendSocialIconsElements))
                            <ul class="social-list">
                                @foreach ($frontendSocialIconsElements as $frontendSocialIconsElement)
                                    <li class="social-list__item">
                                        <a href="{{ $frontendSocialIconsElement->data_values->url }}"
                                           class="social-list__link flex-center" target="_blank">
                                            @php echo $frontendSocialIconsElement->data_values->social_icon @endphp
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-footer py-3">
        <div class="container">
            <div class="row gy-3">
                <div class="col-md-12 text-center">
                    <div class="bottom-footer-text text-white">
                        @lang('Copyright')
                        &copy;<a href="{{ route('home') }}" class="text--base-two">{{ __(gs('site_name')) }}</a>
                        {{ date('Y') }} @lang('All Right Reserved.')
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
