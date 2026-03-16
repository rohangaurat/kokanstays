<div class="booking-filter">
    <form autocomplete="off" class="booking-filter__form">
        <div class="booking-filter__left">

            @if (!request()->routeIs('hotel.details'))
                <div class="form-group grid-form-item mb-0">
                    <div class="grid-form-item__inner">
                        <label class="location-form">
                            <span class="icon"><i class="las la-calendar"></i></span>
                            <span class="fs-12">@lang('City/Country')</span>
                        </label>
                        <select class="form-select select2" name="city_id">
                            <option value="" selected>@lang('Select All')</option>
                            @foreach ($cities ?? [] as $city)
                                <option value="{{ $city->id }}" @selected($city->id == request('city_id'))>
                                    {{ __($city->name) }},
                                    {{ __($city->country->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif


            <div class="form-group grid-form-item mb-0">
                <div class="grid-form-item__inner">
                    <div class="flex-between">
                        <span class="icon">
                            <i class="las la-calendar-check"></i>
                        </span>
                        <div class="check-in-out">
                            <span class="in">@lang('Check In')</span>
                            <span class="out">@lang('Check Out')</span>
                        </div>
                    </div>
                    <div class="t-datepicker">
                        <div class="t-check-in"></div>
                        <div class="t-check-out"></div>
                    </div>
                </div>
            </div>


            <div class="form-group grid-form-item mb-0 total-number-btn">
                <div class="grid-form-item__inner">
                    <div class="roomGuestBtn">
                        <div class="flex-between">
                            <span class="icon">
                                <i class="las la-car"></i>
                            </span>
                            <span class="text">@lang('Rooms & Guests')</span>
                        </div>
                        <input type="hidden" name="rooms">
                        <button type="button" class="total-number roomGuestBtn">
                            <span class="roomCount">0</span> @lang('Room'),
                            <span class="guestCount">0</span> @lang('Guest')
                        </button>
                    </div>
                    <div class="number-picker">
                        <div class="roomElements"></div>
                        <div class="btn--groups">
                            <button type="button" class="btn-outline--base btn btn--sm addRoomBtn">
                                <i class="las la-plus"></i>@lang('Add Room')
                            </button>
                            <button type="button" class="btn--base btn btn--sm doneBtn">
                                @lang('Done')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            

        </div>
        <div class="grid-fit search-btn">
            @if (request()->routeIs('home'))
                <button type="submit" class="btn btn--base w-100 btn--lg">
                    @lang('Search')
                </button>
            @else
                <button type="button" class="btn btn--base w-100 btn--lg" id="commonFilter">
                    @lang('Search')
                </button>
            @endif
        </div>
    </form>
</div>

@push('script')
    <script>
        (function($) {
            'use strict';

            let totalRoom = 0;
            let totalGuest = 0;
            let totalAdult = 2;
            let totalChild = 0;
            let rooms = `{{ request('rooms') }}` ? `{{ request('rooms') }}` : '1,2,0';
            let checkInDateRaw = `{{ request('check_in') ?? '' }}`;
            let checkOutDateRaw = `{{ request('check_out') ?? '' }}`;
            let checkInDate = checkInDateRaw ? new Date(checkInDateRaw) : new Date();
            let checkOutDate = checkOutDateRaw ? new Date(checkOutDateRaw) : new Date();

            if (!checkOutDateRaw) {
                checkOutDate.setDate(checkOutDate.getDate() + 1);
            }

            $('.t-datepicker').tDatePicker({
                dateCheckIn: checkInDate,
                dateCheckOut: checkOutDate,
                iconDate: ``,
                limitNextMonth: 24,
                limitDateRanges: 365,
                autoClose: false,
            });

            $('.t-dates').on('click', function() {
                $('body').addClass('ti-disable-scroll');
                $('.banner-section').addClass('banner-zindex');
            });

            $(document).on('click', function(event) {
                if (!$(event.target).closest('.roomGuestBtn').length) {
                    $('.number-picker').removeClass('number-picker-show');
                }
            });

            $('.roomGuestBtn').on('click', function(event) {
                if ($('.t-datepicker-days').length) {
                    $('.t-datepicker-days').hide();
                    $('.t-arrow-top').hide();
                }
                event.stopPropagation();
                $('.number-picker').toggleClass('number-picker-show');
            });

            function addRoom() {
                return `<div class="room-wrapper edit" data-room_number="${totalRoom}">
                            <div class="room-wrapper__view">
                                <h6 class="title">@lang('Room') ${totalRoom}</h6>
                                <div class="room-wrapper__passenger">
                                    <span class="number">
                                        <span class="adult">0</span> @lang('Adult')
                                    </span>
                                    <span class="room-wrapper__btn-link removeRoom">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="room-wrapper__edit">
                                <div class="top">
                                    <h6 class="title">@lang('Room') ${totalRoom}</h6>
                                    <span class="room-wrapper__btn-link removeRoom">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </span>
                                </div>
                                <ul class="passenger-list">
                                    <li class="passenger-list__item">
                                        <div class="passenger-list__item-left">
                                            @lang('Adults')
                                            <span class="passenger-list__text">
                                                @lang('Above 10 years')
                                            </span>
                                        </div>
                                        <div class="qty-container">
                                            <button class="qty-btn-minus btn-light adultMinusBtn" type="button">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                            <span class="adultQty">0</span>
                                            <button class="qty-btn-plus btn-light adultPlusBtn" type="button">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </li>
                                    <li class="passenger-list__item">
                                        <div class="passenger-list__item-left">
                                            @lang('Child')
                                            <span class="passenger-list__text">0 - 10 @lang('years')</span>
                                        </div>
                                        <div class="qty-container">
                                            <button class="qty-btn-minus btn-light childMinusBtn" type="button">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                            <span class="childQty">0</span>
                                            <button class="qty-btn-plus btn-light childPlusBtn" type="button">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>`;
            }

            function assignRoomNumber() {
                $('.roomElements .room-wrapper').each(function(index) {
                    $(this).find('.totalRoom').text(index + 1);
                    $(this).attr('data-room_number', index + 1);
                    $(this).find('.title').text(`@lang('Room') ${index + 1}`);
                });
            }

            function enableDisableDeleteButton() {
                const show = totalRoom > 1;
                $('.roomElements .room-wrapper .removeRoom').toggle(show);
            }

            function assignRoomsValue(array) {
                let newValue = array.split(',');
                let grouped = [];

                if (rooms != null) {
                    let existingRooms = rooms.split(',');
                    for (let i = 0; i < existingRooms.length; i += 3) {
                        grouped.push(existingRooms.slice(i, i + 3));
                    }
                }
                let isMatched = false;
                grouped.forEach((group, index) => {
                    if (newValue[0] == group[0]) {
                        grouped[index] = newValue;
                        isMatched = true;
                    }
                });

                if (!isMatched) {
                    grouped[grouped.length] = newValue;
                }

                rooms = grouped.map(function(group) {
                    return group.join(',');
                }).join(',');
                $('[name=rooms]').val(rooms);
            }

            function roomInputManage(thisRoomNumber) {
                let thisRoom = $(`[data-room_number=${thisRoomNumber}]`);
                let thisRoomAdult = thisRoom.find('.adultQty').text();
                let thisRoomChild = thisRoom.find('.childQty').text();
                assignRoomsValue(`${thisRoomNumber},${thisRoomAdult},${thisRoomChild}`);
            }

            function deleteAssignedRoomsValue(thisRoomNumber) {
                let roomData = rooms.split(',');
                let grouped = [];
                for (let i = 0; i < roomData.length; i += 3) {
                    grouped.push(roomData.slice(i, i + 3));
                }
                let newGrouped = grouped.filter(group => group[0] != thisRoomNumber);

                newGrouped.forEach((group, index) => {
                    group[0] = (index + 1).toString();
                });
                rooms = newGrouped.map(function(group) {
                    return group.join(',');
                }).join(',');
                $('[name=rooms]').val(rooms);
            }

            if (rooms != null) {
                let roomData = rooms.split(',');
                let grouped = [];

                for (let i = 0; i < roomData.length; i += 3) {
                    grouped.push(roomData.slice(i, i + 3));
                }
                grouped.forEach((group, index) => {
                    totalRoom++;
                    totalGuest += parseInt(group[1]) + parseInt(group[2]);
                    $('.roomElements').append(addRoom());
                    $(`.room-wrapper[data-room_number=${totalRoom}] .adultQty`).text(group[1]);
                    $(`.room-wrapper[data-room_number=${totalRoom}] .adult`).text(group[1]);
                    $(`.room-wrapper[data-room_number=${totalRoom}] .childQty`).text(group[2]);
                });

                rooms = grouped.map(function(group) {
                    return group.join(',');
                }).join(',');
                $('[name=rooms]').val(rooms);

                $('.room-wrapper').removeClass('edit');
                $('.roomElements').children().last().addClass('edit');
                $('.roomCount').text(totalRoom);
                $('.guestCount').text(totalGuest);
                enableDisableDeleteButton();
            }

            $('.addRoomBtn').on('click', function() {
                $('.room-wrapper.edit').removeClass('edit');
                totalRoom++;
                $('.roomElements').append(addRoom());
                $('.roomCount').text(totalRoom);
                enableDisableDeleteButton();
                roomInputManage(totalRoom);
            });

            $(document).on('click', '.removeRoom', function(e) {
                e.stopPropagation();
                if (totalRoom <= 1) return;
                let deletedRoom = $(this).closest('.room-wrapper');
                deleteAssignedRoomsValue(deletedRoom.index() + 1);
                let adultCount = parseInt(deletedRoom.find('.adult').text());
                totalGuest -= adultCount;
                $('.guestCount').text(totalGuest);
                deletedRoom.remove();
                $('.roomElements').children().last().addClass('edit');
                totalRoom--;
                assignRoomNumber();
                enableDisableDeleteButton();
                $('.roomCount').text(totalRoom);
            });

            $(document).on('click', '.room-wrapper__view', function() {
                $('.room-wrapper').removeClass('edit');
                $(this).closest('.room-wrapper').addClass('edit');
            });

            $(document).on('click', '.room-wrapper__edit, .number-picker, .roomGuestBtn', function(e) {
                e.stopPropagation();
            });

            $(document).on('click', '.adultMinusBtn', function() {
                let roomType = $(this).closest('.room-wrapper');
                let adult = parseInt(roomType.find('.adult').text());
                if (adult <= 0 || totalAdult <= 0) return;
                totalAdult--;
                roomType.find('.adult, .adultQty').text(adult - 1);
                totalGuest--;
                $('.guestCount').text(totalGuest);
                roomInputManage(roomType.index() + 1);
            });

            $(document).on('click', '.adultPlusBtn', function() {
                let roomType = $(this).closest('.room-wrapper');
                let adult = parseInt(roomType.find('.adult').text());
                totalAdult++;
                roomType.find('.adult, .adultQty').text(adult + 1);
                totalGuest++;
                $('.guestCount').text(totalGuest);
                roomInputManage(roomType.index() + 1);
            });

            $(document).on('click', '.childMinusBtn', function() {
                let roomType = $(this).closest('.room-wrapper');
                let child = parseInt(roomType.find('.childQty').text());
                if (child == 0 || totalChild <= 0) return;

                totalChild--;
                roomType.find('.childQty').text(child - 1);
                totalGuest--;
                $('.guestCount').text(totalGuest);
                roomInputManage(roomType.index() + 1);
            });

            $(document).on('click', '.childPlusBtn', function() {
                let roomType = $(this).closest('.room-wrapper');
                let child = parseInt(roomType.find('.childQty').text());

                totalChild++;
                roomType.find('.childQty').text(child + 1);
                totalGuest++;
                $('.guestCount').text(totalGuest);
                roomInputManage(roomType.index() + 1);
            });

            $('.doneBtn').on('click', function() {
                $('.number-picker').removeClass('number-picker-show');
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .adultQty,
        .childQty {
            color: hsl(var(--base));
            font-size: 14px;
            font-weight: 700;
        }
    </style>
@endpush
