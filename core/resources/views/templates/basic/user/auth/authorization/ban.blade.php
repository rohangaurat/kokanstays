@extends('Template::layouts.frontend')
@php $userBannedContent = getContent('banned_page.content', true); @endphp
@section('main-content')
    <div class="maintenance-page flex-column justify-content-center">
        <div class="container h-100">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-lg-12 text-center">
                    <img src="{{ frontendImage('user_banned', $userBannedContent->data_values->image ?? null, '700x400') }}"
                         alt="image'" class="mb-4">
                    <h4 class="text--danger mb-2">{{ __($userBannedContent->data_values->heading ?? '') }}</h4>
                    <p class="mb-4">{{ __($user->ban_reason) }} </p>
                    <a href="{{ route('home') }}" class="btn--base btn btn--sm"> @lang('Go to Home') </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .maintenance-page {
            height: 100vh;
        }

        .maintenance-page p {
            width: 50%;
            margin: 0 auto;
        }
    </style>
@endpush


@push('style')
    <style>
        .verification-code-wrapper::after {
            background-color: hsl(var(--white));
        }
    </style>
@endpush
