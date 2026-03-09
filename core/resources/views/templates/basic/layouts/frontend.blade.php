@extends('Template::layouts.app')
@section('main-content')
    @include('Template::partials.header')

    <main>
        @yield('content')
    </main>

    @include('Template::partials.footer')
@endsection

@push('style-lib')
    <link href="{{ asset(activeTemplate(true) . 'css/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset(activeTemplate(true) . 'css/date-picker.min.css') }}" rel="stylesheet">
    <link href="{{ asset(activeTemplate(true) . 'css/date-picker-yellow.css') }}" rel="stylesheet">
    <link href="{{ asset(activeTemplate(true) . 'css/slick.css') }}" rel="stylesheet">
    <link href="{{ asset(activeTemplate(true) . 'css/popup.css') }}" rel="stylesheet">
    <link href="{{ asset(activeTemplate(true) . 'css/odometer.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset(activeTemplate(true) . 'js/jquery-ui.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/datepicker.min.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/slick.min.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/popup.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/odometer.min.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/viewport.jquery.js') }}"></script>
@endpush
