@extends('owner.layouts.app')
@section('panel')
    @php $availableOnly = request()->type && request()->type == 'not_booked'; @endphp
    @if (!$availableOnly)
        <div class="row gy-4">
            @forelse($rooms as $room)
                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6">
                    <div class="widget-two box--shadow2 b-radius--5 bg--white">
                        <div class="widget-two__icon b-radius--5 bg--dark">
                            {{ $room->room->room_number }}
                        </div>
                        <div class="widget-two__content d-flex align-items-center justify-content-between flex-wrap">
                            @if ($room->booking->user_id)
                                <h3><span class="f-size--18 text-center text--dark">{{ $room->booking->user->fullname }}</span></h3>
                            @else
                                <h3 class="f-size--18 text--dark">{{ @$room->booking->guest->name }}</h3>
                            @endif
                            <div class="d-flex flex-column fw-bold w-100">
                                <p class="text--muted text--small">@lang('Booking No.'):
                                    @can('owner.booking.all')
                                        <a class="text--small fw-bold" href="{{ route('owner.booking.details', $room->booking->id) }}">{{ $room->booking->booking_number }}</a>
                                    @else
                                        <span class="fw-bold">{{ $room->booking->booking_number }}</span>
                                    @endcan
                                </p>
                                <p class="text--muted text--small">@lang('Room Type'): {{ __($room->room->roomType->name) }}
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @can('owner.extra.service.add')
                                    <a class="btn btn--xs btn-outline--dark" data-services="{{ $room->extraServices }}" href="{{ route('owner.extra.service.add') }}?room={{ $room->room->room_number }}"> <i class="la la-plus""></i>@lang('Add Service')</a>
                                @endcan
                                <button class="btn btn--xs btn-outline--info btn-view" data-services="{{ $room->extraServices }}" type="button"> <i class="la la-eye""></i>@lang('View Services')</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text--muted text-center">@lang('No room booked yet')</h4>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    @endif

    @if ($emptyRooms->count())
        @if (!$availableOnly)
            <h3 class="mt-5 mb-4">@lang('Available for Booking')</h3>
        @endif
        <div class="row gy-4">
            @foreach ($emptyRooms as $room)
                <div class="col-xxl-2 col-sm-2 col-3">
                    <div class="bg--white p-3 rounded text-center">
                        <span class="d-block fw-bold">
                            {{ $room->room_number }}
                        </span>
                        <span class="text--small">{{ __($room->roomType->name) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    @if ($availableOnly && blank($emptyRooms))
        <div class="col-12">
            <div class="card empty-card">
                <div class="card-body">
                    <div class="text-center message">
                        <i class="las la-file la-3x"></i>
                        <h4>@lang('No room available to book')</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal -->
    <div aria-hidden="true" aria-labelledby="modelTitleId" class="modal fade" id="extraServices" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Extra Services')</h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <h5 class="text--center">@lang('No extra service yet.')</h5>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
    <style>
        .empty-card h4 {
            color: #a5a5a5;
        }

        .message {
            font-size: 38px;
            color: #e9e9e9;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            $('.btn-view').on('click', function() {
                let modal = $('#extraServices');
                let services = $(this).data('services');

                let content = ``;
                if (services.length) {
                    content += `<ul class="list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="w-25">@lang('Name')</span>
                        <span class="w-25 text-center">@lang('Qty')</span>
                        <span class="w-25 text-center">@lang('Price')</span>
                        <span class="w-25 text-end">@lang('Total')</span>
                    </li>
                    `;

                    services.forEach((element, i) => {
                        content += `<li class="list-group-item d-flex justify-content-between">
                            <span class="w-25">${i+1}. ${element.extra_service.name}</span>
                            <span class="w-25 text-center">${element.qty}</span>
                            <span class="w-25 text-center">{{ gs()->cur_sym }}${parseFloat(element.unit_price)}</span>
                            <span class="w-25 text-end">{{ gs()->cur_sym }}${parseFloat(element.total_amount)}</span>
                        </li>`;
                    });

                    content += `</ul>`;
                } else {
                    content = `<h4 class="text-center">@lang('No service used yet!')</h4>`;
                }
                modal.find('.modal-body').html(content);

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
