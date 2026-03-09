@php $user = auth()->user(); @endphp
<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand logo" href="{{ route('home') }}">
                <img src="{{ siteLogo() }}" alt="logo">
            </a>
            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-menu ms-auto align-items-lg-center">
                    <li class="nav-item d-block d-lg-none">
                        <div class="top-button d-flex flex-wrap justify-content-between align-items-center">
                            <ul class="login-registration-list d-flex flex-wrap align-items-center">
                                @include('Template::partials.header_menu')
                            </ul>
                            @include('Template::partials.language_selector')
                        </div>
                    </li>
                    <li class="nav-item {{ menuActive('home') }}">
                        <a class="nav-link" aria-current="page" href="{{ route('home') }}">
                            @lang('Home')
                        </a>
                    </li>
                    <li class="nav-item {{ menuActive('hotel.*') }}">
                        <a class="nav-link" aria-current="page" href="{{ route('hotel.index') }}">
                            @lang('Hotels')
                        </a>
                    </li>
                    <li class="nav-item {{ menuActive('about') }}">
                        <a class="nav-link" aria-current="page" href="{{ route('about') }}">
                            @lang('About')
                        </a>
                    </li>
                    @foreach ($pages as $page)
                        <li class="nav-item {{ request()->segment(1) == $page->slug ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('pages', $page->slug) }}">
                                {{ __($page->name) }}
                            </a>
                        </li>
                    @endforeach
                    <li class="nav-item {{ menuActive('contact') }}">
                        <a class="nav-link" href="{{ route('contact') }}">
                            @lang('Contact')
                        </a>
                    </li>
                </ul>
            </div>
            <div class="header-right">
                <div class="top-button d-flex flex-wrap justify-content-between align-items-center">
                    @include('Template::partials.language_selector')
                    <ul class="login-registration-list d-flex flex-wrap align-items-center">
                        @include('Template::partials.header_menu')
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
