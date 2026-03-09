@extends('Template::layouts.app')
@section('main-content')
    @include('Template::partials.header')

    <main>
        <div class="profile-section py-80 ">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 pe-xxl-4">
                        @include('Template::partials.user_sidebar')
                    </div>
                    <div class="col-xl-9 col-lg-8 ps-xl-4">
                        <div class="d-lg-none"><span class="profile-filter"><i class="fas fa-list"></i></span></div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('Template::partials.footer')
@endsection
