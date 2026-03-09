@extends($activeTemplate . 'layouts.app')
@section('layout')
    @php
        $banned = getContent('banned_page.content', true);
    @endphp

    <section class="mt-5">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="row justify-content-center">
                        <div class="col-sm-6 col-8 col-lg-12 mt-3">
                            <img src="{{ getImage('assets/images/frontend/banned_page/' . @$banned->data_values->image, '700x400') }}"
                                alt="@lang('image')" class="img-fluid mx-auto">
                        </div>
                    </div>
                    <h2 class="text--danger">{{ __(@$banned->data_values->heading) }}</h2>
                    <div class="reason-wrapper">
                        <h4 class="text--dark">@lang('Ban Reason')</h4>
                        <span class="text--danger"> {{ __(auth()->user()->ban_reason) }}</span>
                    </div>
                    <a href="{{ route('home') }}" class="btn--base btn">@lang('Go To Home')</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        body {
            background: unset !important;
        }

        .reason-wrapper {
            margin-top: 8px;
            margin-bottom: 25px;
        }
    </style>
@endpush
