<div class="hotel-details__item widget_component-wrapper" id="scrollHeadingFour">
    <h5 class="title skeleton">@lang('Policy')</h5>
    <div class="policy-item">
        <span class="policy-item__icon">
            <i class="las la-clock skeleton"></i>
        </span>
        <div class="policy-item__content">
            <h6 class="policy-item__title skeleton">@lang('Check-in/Check-out')</h6>
            <p class="policy-item__text skeleton">
                <span class="text-style">
                    @lang('From')
                    {{ showDateTime($hotel->hotelSetting->checkin_time, 'h:i A') }}
                </span>
                |
                <span class="text-style">
                    @lang('Before')
                    {{ showDateTime($hotel->hotelSetting->checkout_time, 'h:i A') }}
                </span>
            </p>
        </div>
    </div>
    @if ($hotel->hotelSetting->instructions ?? false)
        <div class="policy-item">
            <span class="policy-item__icon">
                <i class="las la-check-square skeleton"></i>
            </span>
            <div class="policy-item__content">
                <h6 class="policy-item__title skeleton">@lang('Instructions')</h6>
                <ul class="policy-list">
                    @foreach ($hotel->hotelSetting->instructions as $instruction)
                        <li class="policy-list__item skeleton">
                            {{ __($instruction ?? '') }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if ($hotel->hotelSetting->early_check_in_policy)
        <div class="policy-item">
            <span class="policy-item__icon">
                <i class="las la-calendar-check skeleton"></i>
            </span>
            <div class="policy-item__content">
                <h6 class="policy-item__title skeleton">@lang('Early Check-in')</h6>
                <p class="policy-item__text skeleton">
                    {{ __($hotel->hotelSetting->early_check_in_policy ?? '') }}
                </p>
            </div>
        </div>
    @endif
    @if ($hotel->hotelSetting->child_policy)
        <div class="policy-item">
            <span class="policy-item__icon">
                <i class="las la-smile-beam skeleton"></i>
            </span>
            <div class="policy-item__content">
                <h6 class="policy-item__title skeleton">@lang('Child Policy')</h6>
                <ul class="policy-list">
                    @foreach ($hotel->hotelSetting->child_policy as $childPolicy)
                        <li class="policy-list__item skeleton"> {{ __($childPolicy ?? '') }} </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if ($hotel->hotelSetting->pet_policy)
        <div class="policy-item">
            <span class="policy-item__icon">
                <i class="las la-paw skeleton"></i>
            </span>
            <div class="policy-item__content">
                <h6 class="policy-item__title skeleton">@lang('Pet Policy')</h6>
                <ul class="policy-list">
                    @foreach ($hotel->hotelSetting->pet_policy as $petPolicy)
                        <li class="policy-list__item skeleton">{{ __($petPolicy ?? '') }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if ($hotel->hotelSetting->other_policy)
        <div class="policy-item">
            <span class="policy-item__icon">
                <i class="las la-draw-polygon skeleton"></i>
            </span>
            <div class="policy-item__content">
                <h6 class="policy-item__title skeleton">@lang('Other Policy')</h6>
                <ul class="policy-list">
                    @foreach ($hotel->hotelSetting->other_policy as $otherPolicy)
                        <li class="policy-list__item skeleton">{{ __($otherPolicy ?? '') }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if ($hotel->hotelSetting->cancellation_policy)
        <div class="policy-item">
            <span class="policy-item__icon">
                <i class="las la-ban skeleton"></i>
            </span>
            <div class="policy-item__content">
                <h6 class="policy-item__title skeleton">@lang('Cancellation Policy')</h6>
                <p class="policy-item__text skeleton">
                    {{ __($hotel->hotelSetting->cancellation_policy ?? '') }}
                </p>
            </div>
        </div>
    @endif
</div>
