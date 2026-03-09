@extends('Template::layouts.frontend')
@php $contactContent = getContent('contact_us.content', true); @endphp
@section('content')
    <div class="contact-section py-80">
        <div class="container">
            <div class="contact-top">
                <div class="row gy-4">
                    <div class="col-lg-4 col-sm-6">
                        <div class="contact-item">
                            <div class="contact-item__icon">
                                @php echo $contactContent?->data_values?->address_icon ?? ''; @endphp
                            </div>
                            <div class="contact-item__content">
                                <h5 class="contact-item__title">
                                    {{ __($contactContent?->data_values?->address_title ?? '') }}
                                </h5>
                                <p class="contact-item__desc">
                                    {{ __($contactContent?->data_values?->address ?? '') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="contact-item">
                            <div class="contact-item__icon">
                                @php echo $contactContent?->data_values?->email_icon ?? ''; @endphp
                            </div>
                            <div class="contact-item__content">
                                <h5 class="contact-item__title">
                                    {{ __($contactContent?->data_values?->email_title ?? '') }}
                                </h5>
                                <a href="mailto:{{ $contactContent?->data_values?->email ?? '' }}" class="contact-item__desc">
                                    {{ __($contactContent?->data_values?->email ?? '') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="contact-item">
                            <div class="contact-item__icon">
                                @php echo $contactContent?->data_values?->phone_icon ?? ''; @endphp
                            </div>
                            <div class="contact-item__content">
                                <h5 class="contact-item__title">
                                    {{ __($contactContent?->data_values?->phone_title ?? '') }}
                                </h5>
                                <a href="tel:{{ $contactContent?->data_values?->phone ?? '' }}" class="contact-item__desc">
                                    {{ __($contactContent?->data_values?->phone ?? '') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="contact-bottom">
                <div class="row gy-4">
                    <div class="col-lg-6">
                        <div class="contact-form">
                            <h4 class="contact-form__title">
                                {{ __($contactContent?->data_values?->heading ?? '') }}
                            </h4>
                            <form method="POST" class="verify-gcaptcha disableSubmission">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-6 col-lg-12 col-sm-6 form-group">
                                        <label class="form--label label-two">@lang('Name')</label>
                                        <input type="text" name="name" class="form--control"
                                               value="{{ old('name', $user?->fullname) }}" required>
                                    </div>
                                    <div class="col-xl-6 col-lg-12 col-sm-6 form-group">
                                        <label class="form--label label-two">@lang('Email')</label>
                                        <input type="email" class="form--control" name="email"
                                               value="{{ old('email', $user?->email) }}" @readonly($user && $user->profile_complete) required>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label class="form--label label-two">@lang('Subject')</label>
                                        <input type="text" class="form--control" name="subject"
                                               value="{{ old('subject') }}" required>
                                    </div>
                                    <div class="col-sm-12 form-group">
                                        <label class="form--label label-two">@lang('Message')</label>
                                        <textarea class="form--control" name="message" rows="10" required>{{ old('message') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        @php $class = 'form--label label-two'; @endphp
                                        <x-captcha :class="$class" />
                                    </div>
                                </div>
                                <div class="contact-form__btn mt-1">
                                    <button type="submit" class="btn btn--base">
                                        @lang('Submit Now')
                                        <span class="icon">
                                            <i class="fa-solid fa-arrow-trend-up"></i>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact-bottom__map">
                            <iframe loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps?q={{ $contactContent?->data_values?->latitude ?? '' }},{{ $contactContent?->data_values?->longitude ?? '' }}&hl=en;z=14&output=embed">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection

@push('style')
    <style>
        .contact-item__desc {
            color: hsl(var(--text-color));
        }
    </style>
@endpush
