@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        @can('owner.room.search')
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('owner.room.search') }}" class="formRoomSearch" method="get">
                            <input name="is_reset" type="hidden" value="0">
                            <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
                                <div class="form-group flex-fill">
                                    <label>@lang('Check In - Check Out Date')</label>
                                    <input autocomplete="off" class="datepicker-here form-control bg--white" data-language="en"
                                        data-multiple-dates-separator=" - " data-position='bottom left' data-range="true"
                                        name="date" placeholder="@lang('Select Date')" required type="text">
                                </div>
                                <div class="form-group flex-fill">
                                    <label>@lang('Room Type')</label>
                                    <select class="form-control select2" data-minimum-results-for-search="-1" name="room_type"
                                        required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($roomTypes as $type)
                                            <option value="{{ $type->id }}" @selected(request('room_type') == $type->id)>{{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group flex-fill">
                                    <label>@lang('Room')</label>
                                    <small class="text--info text-sm availableRoomText"></small>
                                    <input class="form-control" name="rooms" placeholder="@lang('How many room?')"
                                        value="{{ request('rooms') }}" required type="text">
                                </div>
                                <div class="form-group flex-fill">
                                    <button class="btn btn--primary w-100 h-45 search" type="submit">
                                        <i class="la la-search"></i>@lang('Search')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
    <div class="row gy-4 mt-4 booking-wrapper @if(!$bookingRequest) d-none @endif">
        <div class="col-lg-8">
            <div class="row gy-4 bookingInfo"></div>
        </div>
        <div class="col-lg-4">
            <div class="card sticky-card">
                <div class="card-header">
                    <div class="card-title mb-0">
                        <h5>@lang('Book Room')</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.room.book') }}" class="booking-form" id="bookingForm" method="POST">
                        @csrf
                        <input type="hidden" name="checkin_date">
                        <input type="hidden" name="checkout_date">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group ">
                                    <label>@lang('Guest Type')</label>
                                    <select class="form-control select2" data-minimum-results-for-search="-1"
                                        name="guest_type">
                                        <option selected value="0">@lang('Walk-In Guest')</option>
                                        <option value="1" @selected($bookingRequest)>@lang('Existing Guest')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 guestInputDiv">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input class="form-control forGuest" name="guest_name" required type="text">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" name="email" value="{{ $bookingRequest->user->email ?? '' }}" required type="email">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Total Adult')</label>
                                    <input type="number" min="1" name="total_adult" value="{{ $bookingRequest->total_adult ?? '' }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Total Child')</label>
                                    <input type="number" min="0" name="total_child" value="{{ $bookingRequest->total_child ?? '' }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 guestInputDiv">
                                <div class="form-group">
                                    <label>@lang('Phone Number')</label>
                                    <input class="form-control forGuest" name="mobile" required type="number">
                                </div>
                            </div>
                            <div class="col-12 guestInputDiv">
                                <div class="form-group">
                                    <label>@lang('Address')</label>
                                    <input class="form-control forGuest" name="address" required type="text">
                                </div>
                            </div>
                            <div class="orderList d-none">
                                <ul class="list-group list-group-flush orderItem">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <h6>@lang('Room')</h6>
                                        <h6>@lang('Days')</h6>
                                        <span>
                                            <h6>@lang('Fare')</h6>
                                        </span>
                                        <span>
                                            <h6>@lang('Total')</h6>
                                        </span>
                                    </li>
                                </ul>
                                <div class="d-flex justify-content-between align-items-center border-top p-2">
                                    <span>@lang('Subtotal')</span>
                                    <span class="totalFare"></span>
                                </div>
                                <div
                                    class="d-flex justify-content-between align-items-center border-top p-2 discountLi d-none">
                                    <span>@lang('Discount')</span>
                                    <span class="totalDiscount"></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-top p-2">
                                    <span>{{ hotelSetting('tax_name') }}
                                        <small>({{ hotelSetting('tax_percentage') }}%)</small></span>
                                    <span><span class="taxCharge"
                                            data-percent_charge="{{ hotelSetting('tax_percentage') }}"></span>
                                        {{ __(gs()->cur_text) }}</span>
                                    <input name="tax_charge" type="hidden">
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-top p-2">
                                    <span>@lang('Grand Total')</span>
                                    <span class="grandTotalFare"></span>
                                    <input hidden name="total_amount" type="text">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Paying Amount')</label>
                                    <input class="form-control" min="0" name="paid_amount"
                                        placeholder="@lang('Paying Amount')" step="any" type="number">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group ">
                                    <label>@lang('Payment System')</label>
                                    <select class="form-control select2" data-minimum-results-for-search="-1"
                                        name="payment_system_id">
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($paymentSystems as $item)
                                            <option value="{{ $item->id }}">{{ __($item->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @can('owner.room.book')
                                <div class="form-group mb-0">
                                    <button class="btn btn--primary h-45 w-100 btn-book confirmBookingBtn"
                                        type="button">@lang('Book Now')</button>
                                </div>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmBookingModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure to book this rooms?')</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--dark" data-bs-dismiss="modal" type="button">@lang('No')</button>
                    <button class="btn btn--primary btn-confirm" type="button">@lang('Yes')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@can('owner.booking.all')
    @push('breadcrumb-plugins')
        <a class="btn btn-sm btn--primary" href="{{ route('owner.booking.all') }}">
            <i class="la la-list"></i>@lang('All Bookings')
        </a>
    @endpush
@endcan

@push('script-lib')
    <script src="{{ asset('assets/global/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush

@push('style')
    <style>
        .booking-table td {
            white-space: unset;
        }

        .modal-open .select2-container {
            z-index: 9 !important;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";

        $('[name=guest_type]').on('change', function() {
            if ($(this).val() == 1) {
                $('.guestInputDiv').addClass('d-none');
                $('.forGuest').attr("required", false);
            } else {
                $('.guestInputDiv').removeClass('d-none');
                $('.forGuest').attr("required", true);
            }
        });

        $('[name=room_type]').on('change', function() {
            getAvailableRoom();
        });

        $('[name=date]').daterangepicker({
            autoUpdateInput: false,
            minDate: moment(),
            locale: {
                cancelLabel: 'Clear'
            }
        });

        const changeDatePickerText = (event, startDate, endDate) => {
            $(event.target).val(startDate.format('MM/DD/YYYY') + ' - ' + endDate.format('MM/DD/YYYY'));
            getAvailableRoom();
        }

        $('[name=date]').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));

        @if ($bookingRequest ?? null)
            $('[name="date"]').val(
                moment('{{ $bookingRequest->check_in }}').format('MM/DD/YYYY') +
                ' - ' +
                moment('{{ $bookingRequest->check_out }}').format('MM/DD/YYYY')
            );
            $('[name="guest_type"]').val('1').trigger('change');
        @endif

        function getAvailableRoom() {
            let searchDate = $('[name=date]').val();
            let roomType = $('[name=room_type]').val();

            if (searchDate.split(" - ").length < 2 || !roomType) {
                $('.availableRoomText').html('');
                return false;
            }

            let checkIn = searchDate.split(" - ")[0];
            let checkOut = searchDate.split(" - ")[1];

            $.ajax({
                type: "GET",
                url: "{{ route('owner.room.available') }}",
                data: {
                    checkin_date: checkIn,
                    checkout_date: checkOut,
                    room_type: roomType,
                    get_available: true

                },
                success: function(response) {
                    if (response.status) {
                        $('.availableRoomText').html(`@lang('Available Rooms'): ${response.available_rooms}`);
                    }
                }
            });
        }

        $('.formRoomSearch').on('submit', function(e) {
            e.preventDefault();

            let searchDate = $('[name=date]').val();
            if (searchDate.split(" - ").length < 2) {
                notify('error', `@lang('Check-In date and checkout date should be given for booking.')`);
                return false;
            }

            let formData = $(this).serialize();
            let url = $(this).attr('action');

            $.ajax({
                type: "get",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.status) {
                        $('.bookingInfo').append(response.html);
                        $('#room_type').val('');
                        $('.booking-wrapper').removeClass('d-none');
                        $('.availableRoomText').html("");
                        $('#bookingForm').find('[name=checkin_date]').val(response.check_in);
                        $('#bookingForm').find('[name=checkout_date]').val(response.check_out);
                        updateOrderList();
                    } else {
                        notify('error', response.error);
                    }
                },
                processData: false,
                contentType: false,
            });
        });

        $(document).on('click', '.removeRoomTypeBtn', function() {
            let roomType = $(this).data('room_type_id');
            $(this).parents('.parentDiv').remove();
            $(document).find(`.order-list-type-${roomType}`).remove();

            $.ajax({
                type: "post",
                url: "{{ route('owner.room.session.data.update') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    room_type: roomType
                },
                success: function(response) {
                    updateOrderList();
                }
            });
        });

        $(document).on('submit', '.resetRoomForm', function(e) {
            e.preventDefault();
            var parent = $(this).parents('.parentDiv');
            var resetRoomTypeId = $(this).find('[name=reset_room_type_id]').val();
            var resetNumberOfRooms = $(this).find('[name=reset_number_of_rooms]').val();
            var searchDate = $('[name=date]').val();

            if (resetNumberOfRooms <= 0) {
                notify('error', "@lang('Number of rooms can\'t less than or equal to zero')");
                return false;
            }

            $.ajax({
                type: "get",
                url: "{{ route('owner.room.search') }}",
                data: {
                    date: searchDate,
                    room_type: resetRoomTypeId,
                    rooms: resetNumberOfRooms,
                    is_reset: 1
                },
                success: function(response) {
                    if (response.status) {
                        parent.replaceWith(response.html);
                        updateOrderList();
                    } else {
                        notify('error', response.error);
                    }
                },
                contentType: false,
            });
        });
    </script>
    <script src="{{ asset('assets/owner/js/booking.js') }}"></script>
@endpush
