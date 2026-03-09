@foreach ($bookingRequest->bookingRequestDetails as $item)
    @php
        $bookedRooms = [];
        $numberOfRooms = $item->number_of_rooms;
        $checkIn = \Carbon\Carbon::parse($bookingRequest->check_in);
        $checkout = \Carbon\Carbon::parse($bookingRequest->check_out);
        $roomType = App\Models\RoomType::with('images')->find($item->room_type_id);

        $rooms = App\Models\Room::active()
            ->where('room_type_id', $item->room_type_id)
            ->with([
                'booked' => function ($q) {
                    $q->active();
                },
                'roomType:id,name,fare,discount_percentage',
                'roomType.images',
            ])
            ->get();
    @endphp
    <div class="col-lg-12 parentDiv">
        <div class="card roomType" data-room_type="{{ $roomType->id }}">
            <div class="card-header">
                <div class=" d-flex justify-content-between flex-wrap gap-2">
                    <div class="card-title">
                        <h5 class="mb-0">{{ __($roomType->name) }}</h5>
                        <small>@lang('Selected Room'): {{ $numberOfRooms }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="pb-3">
                    <span class="fas fa-circle text--danger"></span>
                    <span class="mr-5">@lang('Booked')</span>
                    <span class="fas fa-circle text--success"></span>
                    <span class="mr-5">@lang('Selected')</span>
                    <span class="fas fa-circle text--primary"></span>
                    <span>@lang('Available')</span>
                </div>

                <div class="table-responsive table-responsive--sm">
                    <table class="table-bordered booking-table table">
                        <thead>
                            <tr>
                                <th>@lang('Date')</th>
                                <th>@lang('Room')</th>
                            </tr>
                        </thead>
                        <tbody class="room-table">
                            @php
                                $selectedRooms = [];
                            @endphp
                            <tr>
                                <td class="text-center" colspan="2">
                                    <span class="fw-bold">{{ __($item->roomType->name) }}</span> <br>
                                    <small class="text--info">@lang('Total Room'): {{ $item->number_of_rooms }}</small>
                                </td>
                            </tr>

                            @while ($checkIn < $checkout)
                                <tr>
                                    <td class="text-center">{{ $checkIn->format('d M, Y') }} - {{ (clone $checkIn)->addDay()->format('d M, Y') }}</td>
                                    <td class="room-column @if ($bookingRequest->check_in == $checkIn->format('Y-m-d')) first-column-rooms @endif" data-number_of_rooms="{{ $item->number_of_rooms }}">
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
                                                    <button class="btn btn--primary btn-sm room-btn available" data-booked_status="0" data-date="{{ $checkIn->format('m/d/Y') }}" data-discount="{{ $room->discountAmount() }}" data-room="{{ $room }}" data-room_type_id="{{ $room->roomType->id }}" data-unit_fare="{{ getAmount($item->unit_fare) }}" room="room-{{ $room->room_number }}">
                                                        {{ $room->room_number }}
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @php
                                    $checkIn = \Carbon\Carbon::parse($checkIn)->addDay();
                                @endphp
                            @endwhile
                            @php
                                $selectedRooms = array_merge($selectedRooms, getSelectedRooms($bookedRooms, $numberOfRooms));
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
@endforeach
