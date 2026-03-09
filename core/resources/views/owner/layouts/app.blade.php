@extends('owner.layouts.master')
@section('content')
    <div class="page-wrapper default-version">
        @include('owner.partials.sidenav')
        @include('owner.partials.topnav')
        <div class="body-wrapper">
            <div class="bodywrapper__inner">
                @include('owner.partials.breadcrumb')
                @yield('panel')
            </div>
        </div>
    </div>
@endsection
