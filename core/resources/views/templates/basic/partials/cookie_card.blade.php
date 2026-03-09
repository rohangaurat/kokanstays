@php $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first(); @endphp
@if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
    <div class="cookies-card text-center">
        <div class="cookies-card__icon bg--base"><i class="las la-cookie-bite"></i></div>
        <p class="cookies-card__desc text-start">
            {{ __($cookie->data_values->short_desc ?? '') }}
            <a class="text--base" href="{{ route('cookie.policy') }}" target="_blank">@lang('Learn more')</a>
        </p>
        <div class="cookies-card__btn">
            <button class="btn btn--base btn--sm policy">@lang('Allow')</button>
            <button class="btn btn-outline--secondary btn--sm policy">@lang('Reject')</button>
        </div>
    </div>
@endif
