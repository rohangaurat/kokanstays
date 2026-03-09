@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between">
                    <h5 class="card-title">@lang('Booked Rooms') </h5>
                    <span class="fw-bold">#{{ $booking->booking_number }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('SL')</th>
                                    <th>@lang('Room Number')</th>
                                    <th>@lang('Room Type')</th>
                                    <th>@lang('Fare')</th>
                                    <th>@lang('Cancellation Fee')</th>
                                    <th>@lang('Refundable')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->activeBookedRooms as $bookedRoom)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $bookedRoom->room->room_number }}</td>
                                        <td> {{ $bookedRoom->room->roomType->name }}</td>
                                        <td>{{ showAmount($bookedRoom->fare) }}</td>
                                        <td>{{ showAmount($bookedRoom->cancellation_fee) }}</td>
                                        <td>{{ showAmount($bookedRoom->fare - $bookedRoom->cancellation_fee) }}</td>
                                    </tr>
                                @endforeach

                            </tbody>

                            <tfoot>
                                @php
                                    $cancellationFee = $booking->activeBookedRooms->sum('cancellation_fee');
                                    $refundable = $booking->paid_amount - $cancellationFee;
                                    $notificationText = $refundable >= 0 ? 'Refundable' : 'Due'
                                @endphp
                                <tr>
                                    <th class="text-end" colspan="4">@lang('Total')</th>
                                    <th>{{ showAmount($cancellationFee) }}</th>
                                    <th>{{ showAmount($booking->activeBookedRooms->sum('fare') - $cancellationFee) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

                <div class="card-footer">
                    @can('owner.booking.cancel.full')
                        <form action="{{ route('owner.booking.cancel.full', $booking->id) }}" method="post">
                            @csrf
                            <button class="btn btn--primary h-45 w-100" type="submit">@lang('Confirm Cancellation')</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
