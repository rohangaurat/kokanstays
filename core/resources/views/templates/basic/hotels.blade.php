@extends('Template::layouts.frontend')
@php
    if (session()->has('popular_hotels')) {
        session()->forget('popular_hotels');
    }

    if (session()->has('featured_hotels')) {
        session()->forget('featured_hotels');
    }
@endphp
@section('content')
    <div class="filter-section">
        <div class="container">
            @include('Template::partials.booking_filter')
        </div>
    </div>
    <div class="hotel-section">
        <div class="container">
            <div class="hotel-main-wrapper">
                @include('Template::partials.hotel_sidebar')
                <div class="hotel-main-wrapper__body">
                    <div class="d-xl-none d-inline-block">
                        <div class="filter-icon">
                            <i class="las la-list"></i>
                        </div>
                    </div>
                    <div class="hotelLists">
                        @include('Template::partials.hotel_list_card')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

            let cityId = $('[name="city_id"]').val();
            let checkIn = $('[name="check_in"]').val();
            let checkOut = $('[name="check_out"]').val();
            let rooms = $('[name="rooms"]').val();
            let minFare = $('[name="min_fare"]').val();
            let maxFare = $('[name="max_fare"]').val();
            let userRating = [];
            let bedType = [];
            let facilities = [];
            let amenities = [];
            let hotelStar = $('[name="hotel_star"]').val();
            let popular = `{{ request('popular') }}` ? true : false;
            let featured = `{{ request('featured') }}` ? true : false;
            let page = '';

            $('.booking-filter__form').attr('action', "{{ route('hotel.index') }}");

            function updateUrl() {
                const urlParams = new URLSearchParams(window.location.search);
                if (page) {
                    urlParams.set('page', page);
                } else {
                    urlParams.delete('page');
                }
                const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
                window.history.replaceState({}, '', newUrl);
            }

            function filterHotels() {
                const formData = {
                    min_fare: minFare,
                    max_fare: maxFare,
                    user_rating: userRating,
                    hotel_star: hotelStar,
                    bed_type: bedType,
                    facilities: facilities,
                    amenities: amenities,
                    city_id: $('[name="city_id"]').val(),
                    check_in: $('[name="check_in"]').val(),
                    check_out: $('[name="check_out"]').val(),
                    rooms: $('[name="rooms"]').val(),
                    popular: popular,
                    featured: featured,
                    filter: true,
                    page: page
                };
                $.ajax({
                    url: "{{ route('hotel.index') }}",
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                        $('.hotelLists').html(response.view);
                        page = response.page ?? page;
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);
                        updateUrl();
                        setTimeout(function() {
                            $('.skeleton').removeClass('skeleton');
                        }, 1000);
                    }
                });
            }

            $(document).on('change', '[name="popular"]', function(e) {
                e.preventDefault();
                if ($('[name="popular"]:checked').length > 0) {
                    popular = true;
                } else {
                    popular = false;
                }
                filterHotels();
            });

            $(document).on('change', '[name="featured"]', function(e) {
                e.preventDefault();
                if ($('[name="featured"]:checked').length > 0) {
                    featured = true;
                } else {
                    featured = false;
                }
                filterHotels();
            });

            $(document).on('click', '.filterResetBtn', function(e) {
                e.preventDefault();
                $('[name="min_fare"]').val('').attr('placeholder', '00.00');
                minFare = $('[name="min_fare"]').val();
                $('[name="max_fare"]').val('').attr('placeholder', '00.00');
                maxFare = $('[name="max_fare"]').val();
                $('[name="user_rating[]"]').prop('checked', false);
                userRating = [];
                $('[name="bed_type[]"]').prop('checked', false);
                bedType = [];
                $('[name="facilities[]"]').prop('checked', false);
                facilities = [];
                $('[name="amenities[]"]').prop('checked', false);
                amenities = [];
                $('[name="hotel_star"]').prop('checked', false);
                $('[name="hotel_star"][value="0"]').prop('checked', true);
                hotelStar = 0;
                popular = false;
                $('[name="popular"]').prop('checked', false);
                featured = false;
                $('[name="featured"]').prop('checked', false);
                filterHotels();
            });

            $(document).on('click', '#commonFilter', function(e) {
                e.preventDefault();
                let form = $('.booking-filter__form');
                let action = form.attr('action');
                let data = form.serialize();
                let checkOutMatch = data.match(/check_out=([^&]*)/);
                if (checkOutMatch[1] == 'null') {
                    notify('error', 'Please select check out date');
                    return false;
                }

                let fullUrl = `${action}?${data}`;
                $.ajax({
                    url: fullUrl,
                    type: 'GET',
                    data: {
                        min_fare: minFare,
                        max_fare: maxFare,
                        user_rating: userRating,
                        hotel_star: hotelStar,
                        bed_type: bedType,
                        facilities: facilities,
                        amenities: amenities,
                        popular: popular,
                        featured: featured,
                        filter: true
                    },
                    success: function(response) {
                        $('.hotelLists').html(response.view);
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);
                        setTimeout(function() {
                            $('.skeleton').removeClass('skeleton');
                        }, 1000);
                        window.history.pushState({}, '', fullUrl);
                        page = '';
                        updateUrl();
                    }
                });
            });

            $(document).on('click', '.priceFilterBtn', function(e) {
                e.preventDefault();
                minFare = $('[name="min_fare"]').val();
                maxFare = $('[name="max_fare"]').val();
                if (minFare == '') {
                    notify('error', 'Please enter minimum fare');
                    return false;
                }
                if (maxFare == '') {
                    notify('error', 'Please enter maximum fare');
                    return false;
                }
                filterHotels();
            });

            $(document).on('change', '[name="user_rating[]"]', function(e) {
                e.preventDefault();
                userRating = $('[name="user_rating[]"]:checked').map(function() {
                    return $(this).val();
                }).get();
                filterHotels();
            });

            $(document).on('change', '[name="bed_type[]"]', function(e) {
                e.preventDefault();
                bedType = $('[name="bed_type[]"]:checked').map(function() {
                    return $(this).val();
                }).get();
                filterHotels();
            });

            $(document).on('change', '[name="facilities[]"]', function(e) {
                e.preventDefault();
                facilities = $('[name="facilities[]"]:checked').map(function() {
                    return $(this).val();
                }).get();
                filterHotels();
            });

            $(document).on('change', '[name="amenities[]"]', function(e) {
                e.preventDefault();
                amenities = $('[name="amenities[]"]:checked').map(function() {
                    return $(this).val();
                }).get();
                filterHotels();
            });

            $(document).on('change', '[name="hotel_star"]', function(e) {
                e.preventDefault();
                hotelStar = $('[name="hotel_star"]:checked').val();
                filterHotels();
            });

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                let href = $(this).attr('href');
                let url = new URL(href);
                page = url.searchParams.get("page");
                filterHotels();
            });

            setTimeout(function() {
                $('.skeleton').removeClass('skeleton');
            }, 1000);
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .booking-card__thumb img{
            max-height: 170px;
        }
    </style>
@endpush
