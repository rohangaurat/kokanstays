@php
    $bookedRooms = [];
    $i = 0;
@endphp

<div class="col-lg-12 parentDiv">
    <div class="card roomType" data-room_type="{{ $roomType->id }}">
        <div class="card-header position-relative">

            <button class="btn btn--danger removeRoomTypeBtn" data-room_type_id="{{ $roomType->id }}"><i class="las la-times me-0"></i></button>

            <div class=" d-flex justify-content-between flex-wrap gap-2">
                <div class="card-title">
                    <h5 class="mb-0">{{ __($roomType->name) }}</h5>
                    <small>@lang('Selected Room'): {{ $numberOfRooms }}, @lang('Total Available'): {{ $roomType->available_rooms }}</small>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2">
                    <form action="" class="resetRoomForm" method="GET">
                        <input name="reset_room_type_id" type="hidden" value="{{ $roomType->id }}">
                        <div class="row align-items-center">
                            <div class="col-lg-3">
                                <label>@lang('Rooms')</label>
                            </div>

                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input class="form-control" min="1" name="reset_number_of_rooms" type="number" value="{{ $numberOfRooms }}">
                                    <button class="input-group-text" type="submit">@lang('Update')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div class="pb-3">
                    <span class="fas fa-circle text--danger"></span>
                    <span class="mr-5">@lang('Booked')</span>
                    <span class="fas fa-circle text--success"></span>
                    <span class="mr-5">@lang('Selected')</span>
                    <span class="fas fa-circle text--primary"></span>
                    <span>@lang('Available')</span>
                </div>

                <div class="pb-3">
                    <small>@lang('Capacity'): {{ $roomType->total_adult. ' '. Str::plural('Adult', $roomType->total_adult). ' , '. $roomType->total_child. ' '. Str::plural('Child', $roomType->total_child) }} </small>
                </div>
            </div>

            <div class="table-responsive table-responsive--sm">
                <table class="table-bordered booking-table table">
                    <thead>
                        <tr>
                            <th>@lang('Date')</th>
                            <th>@lang('Room')</th>
                        </tr>
                    </thead>
                    <tbody class="room-table-body">
                        @while ($checkIn < $checkOut)
                            <tr class="table-row" data-room_type_id="{{ $roomType->id }}">
                                <td class="text-center">{{ $checkIn->format('d M, Y') }} - {{ (clone $checkIn)->addDay()->format('d M, Y') }}</td>
                                <td class="room-column @if ($i == 0) first-column-rooms @endif" data-number_of_rooms="{{ $numberOfRooms }}" data-room_type_id="{{ $roomType->id }}">
                                    <div class="d-flex w-100 flex-wrap gap-2">
                                        @foreach ($rooms as $room)
                                            @php
                                                $bookedRooms[$room->room_number] = $bookedRooms[$room->room_number] ?? 0;
                                                $booked = $room->booked->where('booked_for', $checkIn->format('Y-m-d'))->first();
                                            @endphp

                                            @if ($booked)
                                                <button class="btn btn--danger btn-sm room-btn" disabled room="room-{{ $room->room_number }}">{{ $room->room_number }}</button>
                                                @php
                                                    $bookedRooms[$room->room_number]++;
                                                @endphp
                                            @else
                                                <button class="btn btn--primary btn-sm room-btn available" data-booked_status="0" data-unit_fare="{{ getAmount($room->roomType->fare) }}" data-discount="{{ $room->discountAmount() }}" data-room_type_id="{{ $room->roomType->id }}" data-date="{{ $checkIn->format('m/d/Y') }}"
                                                    room="room-{{ $room->room_number }}" data-room="{{ $room }}">
                                                    {{ $room->room_number }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @php
                                $checkIn = \Carbon\Carbon::parse($checkIn)->addDays();
                                $i++;
                            @endphp
                        @endwhile

                        @php
                            $selectedRooms = getSelectedRooms($bookedRooms, $numberOfRooms);
                        @endphp
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        (function($) {
            'use strict';
            var selectedRooms = @json($selectedRooms);
            var roomType = @json($roomType);

            selectRooms(roomType, selectedRooms);
        })(jQuery);
    </script>
</div>
