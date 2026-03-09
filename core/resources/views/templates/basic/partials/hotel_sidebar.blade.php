<div class="hotel-sidebar">
    <span class="sidebar-filter__close d-lg-none d-block"><i class="fas fa-times"></i></span>
    <div class="sidebar-header">
        <button class="sidebar-header__filter">@lang('Filter')</button>
        <button type="reset" class="btn btn--base btn--sm filterResetBtn">
            <span class="btn-icon"><i class="las la-reply"></i></span> @lang('Reset')
        </button>
    </div>
    <div class="accordion sidebar--acordion">
        <div class="filter-block">
            <div class="accordion-item">
                <div id="general" class="accordion-collapse">
                    <div class="accordion-body">
                        <ul class="filter-block__list">
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="featured" value="featured" type="checkbox"
                                        id="featured" @checked(request('featured'))>
                                    <label class="form-check-label" for="featured">
                                        <span class="label-text">@lang('Featured')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="popular" value="popular"  type="checkbox"
                                        id="popular" @checked(request('popular'))>
                                    <label class="form-check-label" for="popular">
                                        <span class="label-text">@lang('Most Popular')</span>
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-block">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#Level"
                        aria-expanded="true">
                        @lang('User Rating')
                    </button>
                </h2>
                <div id="Level" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <ul class="filter-block__list">
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="5" type="checkbox"
                                        id="rating_5" @checked(in_array('5', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_5">
                                        <span class="label-text">5.0 @lang('Outstanding')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="4.5" type="checkbox"
                                        id="rating_45" @checked(in_array('4.5', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_45">
                                        <span class="label-text">4.5 @lang('Excellent')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="4" type="checkbox"
                                        id="rating_4" @checked(in_array('4', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_4">
                                        <span class="label-text">4.0 @lang('Very Good')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="3.5" type="checkbox"
                                        id="rating_35" @checked(in_array('3.5', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_35">
                                        <span class="label-text">3.5 @lang('Decent')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="3" type="checkbox"
                                        id="rating_3" @checked(in_array('3', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_3">
                                        <span class="label-text">3.0 @lang('Average')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="2.5" type="checkbox"
                                        id="rating_25" @checked(in_array('2.5', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_25">
                                        <span class="label-text">2.5 @lang('Fair')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="2" type="checkbox"
                                        id="rating_2" @checked(in_array('2', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_2">
                                        <span class="label-text">2.0 @lang('Poor')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="1.5" type="checkbox"
                                        id="rating_15" @checked(in_array('1.5', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_15">
                                        <span class="label-text">1.5 @lang('Very Poor')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="filter-block__item">
                                <div class="form--check">
                                    <input class="form-check-input" name="user_rating[]" value="1"
                                        type="checkbox" id="rating_1" @checked(in_array('1', request('user_rating', [])))>
                                    <label class="form-check-label" for="rating_1">
                                        <span class="label-text">1.0 @lang('Awful')</span>
                                    </label>
                                </div>
                            </li>
                            <li class="load-more-button">@lang('Load more')</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-block">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#topic" aria-expanded="true">
                        @lang('Price Range')
                    </button>
                </h2>
                <div id="topic" class="accordion-collapse show collapse">
                    <div class="accordion-body">
                        <ul class="filter-block__list">
                            <li class="filter-block__item">
                                <div class="price-range">
                                    <input type="number" class="form--control" name="min_fare"
                                        value="{{ request('min_fare') }}" placeholder="00.00">
                                    <input type="number" class="form--control" name="max_fare"
                                        value="{{ request('max_fare') }}" placeholder="00.00">
                                </div>
                                <button class="btn--secondary btn w-100 priceFilterBtn">@lang('Price Filter')</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-block">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#rating" aria-expanded="true">
                        @lang('Hotel Star Category')
                    </button>
                </h2>
                <div id="rating" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <ul class="filter-block__list">
                            <li class="filter-block__item">
                                <div class="rating-menu">
                                    <div class="form-check form--radio w-100">
                                        <label class="form-check-label" for="hotelStarZero">
                                            <input class="form-check-input" type="radio" name="hotel_star"
                                                value="0" id="hotelStarZero" @checked(request('hotel_star') == 0)>
                                            <x-rating-star :rating="0" />
                                            @lang('All')
                                        </label>
                                    </div>
                                    <div class="form-check form--radio w-100">
                                        <label class="form-check-label" for="hotelStarFive">
                                            <input class="form-check-input" type="radio" name="hotel_star"
                                                value="5" id="hotelStarFive" @checked(request('hotel_star') == 5)>
                                            <x-rating-star :rating="5" />
                                            5
                                        </label>
                                    </div>
                                    <div class="form-check form--radio w-100">
                                        <label class="form-check-label" for="hotelStarFour">
                                            <input class="form-check-input" type="radio" name="hotel_star"
                                                value="4" id="hotelStarFour" @checked(request('hotel_star') == 4)>
                                            <x-rating-star :rating="4" />
                                            4
                                        </label>
                                    </div>
                                    <div class="form-check form--radio w-100">
                                        <label class="form-check-label" for="hotelStarThree">
                                            <input class="form-check-input" type="radio" name="hotel_star"
                                                value="3" id="hotelStarThree" @checked(request('hotel_star') == 3)>
                                            <x-rating-star :rating="3" />
                                            3
                                        </label>
                                    </div>
                                    <div class="form-check form--radio w-100">
                                        <label class="form-check-label" for="hotelStarTwo">
                                            <input class="form-check-input" type="radio" name="hotel_star"
                                                value="2" id="hotelStarTwo" @checked(request('hotel_star') == 2)>
                                            <x-rating-star :rating="2" />
                                            2
                                        </label>
                                    </div>
                                    <div class="form-check form--radio w-100">
                                        <label class="form-check-label" for="hotelStarOne">
                                            <input class="form-check-input" type="radio" name="hotel_star"
                                                value="1" id="hotelStarOne" @checked(request('hotel_star') == 1)>
                                            <x-rating-star :rating="1" />
                                            1
                                        </label>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @if (!blank($bedTypes))
            <div class="filter-block">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#Price" aria-expanded="true">
                            @lang('Bed Type')
                        </button>
                    </h2>
                    <div id="Price" class="accordion-collapse show collapse">
                        <div class="accordion-body">
                            <ul class="filter-block__list">
                                @foreach ($bedTypes as $bedType)
                                    <li class="filter-block__item">
                                        <div class="form--check">
                                            <input class="form-check-input" type="checkbox" name="bed_type[]"
                                                value="{{ $bedType->name }}" id="bedType_{{ $bedType->id }}">
                                            <label class="form-check-label" for="bedType_{{ $bedType->id }}">
                                                <span class="label-text">
                                                    {{ __($bedType->name) }}
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                                <li class="load-more-button">@lang('Load more')</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (!blank($facilities))
            <div class="filter-block">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#facilitiesTwo" aria-expanded="true">
                            @lang('Facilities')
                        </button>
                    </h2>
                    <div id="facilitiesTwo" class="accordion-collapse show collapse">
                        <div class="accordion-body">
                            <ul class="filter-block__list">
                                @foreach ($facilities as $facility)
                                    <li class="filter-block__item">
                                        <div class="form--check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]"
                                                value="{{ $facility->id }}" id="facility_{{ $facility->id }}">
                                            <label class="form-check-label" for="facility_{{ $facility->id }}">
                                                <span class="label-text">{{ __($facility->name) }}</span>
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                                <li class="load-more-button">@lang('Load mores')</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (!blank($amenities))
            <div class="filter-block">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#facilities" aria-expanded="true">
                            @lang('Amenities')
                        </button>
                    </h2>
                    <div id="facilities" class="accordion-collapse show collapse">
                        <div class="accordion-body">
                            <ul class="filter-block__list">
                                @foreach ($amenities as $amenity)
                                    <li class="filter-block__item">
                                        <div class="form--check">
                                            <input class="form-check-input" type="checkbox" name="amenities[]"
                                                value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}">
                                            <label class="form-check-label" for="amenity_{{ $amenity->id }}">
                                                <span class="label-text">{{ __($amenity->title) }}</span>
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                                <li class="load-more-button">@lang('Load more')</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
