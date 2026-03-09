<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ gs()->siteName('Invoice') }}</title>
    <link href="{{ siteFavicon() }}" rel="shortcut icon" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/owner/css/invoice.css') }}">
</head>

<body>
    @php
        $extraService = count($booking->usedExtraService);
        $due = $booking->total_amount - $booking->paid_amount;
        $bookedRooms = $booking->bookedRooms->groupBy('booked_for');
        $totalFare = $booking->bookedRooms->sum('fare');
        $totalTaxCharge = $booking->bookedRooms->sum('tax_charge');
        $canceledFare = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('fare');
        $canceledTaxCharge = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('tax_charge');
    @endphp
    <header>
        <div class="row">
            <div class="col-12">
                <div class="list--row">
                    <div class="logo float-left">
                        <img alt="@lang('image')" class="logo-img" src="{{ siteLogo('dark') }}">
                    </div>
                    <h6 class="float-right m-0" style="margin: 0;"> {{ date('Y-m-d') }}</h6>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="row">
            <div class="col-12">
                <div class="address list--row">
                    <div class="float-left">
                        <h5 class="primary-text d-block fw-md">@lang('Invoice To')</h5>
                        <ul class="list" style="--gap: 0.3rem">
                            <li>
                                <div class="list list--row gap-5rem">
                                    <span class="strong">@lang('Name') :</span>
                                    <span>{{ $booking->user ? $booking->user->fullname : $booking->guest->name }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row gap-5rem">
                                    <span class="strong">@lang('Email') :</span>
                                    <span>{{ $booking->user ? $booking->user->email : $booking->guest->email }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row gap-5rem">
                                    <span class="strong">@lang('Mobile') :</span>
                                    <span>+{{ $booking->user ? $booking->user->mobile : $booking->guest->mobile }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="float-right">
                        <ul class="text-end">
                            <li>
                                <h5 class="primary-text d-block fw-md"> @lang('Bill Information') </h5>
                            </li>

                            <li>
                                <span class="d-inline-block strong">@lang('Booking No') :</span>
                                <span class="d-inline-block">{{ $booking->booking_number }}</span>
                            </li>

                            <li>
                                <span class="d-inline-block strong">@lang('Booking Date') :</span>
                                <span class="d-inline-block">{{ showDateTime($booking->created_at) }}</span>
                            </li>

                            <li>
                                <span class="d-inline-block strong">@lang('Total Amount') :</span>
                                <span class="d-inline-block">{{ showAmount($booking->total_amount) }}</span>
                            </li>
                            <li>
                                <span class="d-inline-block strong">@lang('Paid Amount') :</span>
                                <span class="d-inline-block">{{ showAmount($booking->paid_amount) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="body">
                    <h5 class="title">@lang('Room\'s Details')</h5>
                    <table class="table-bordered custom-table table">
                        <thead>
                            <tr>
                                <th>@lang('Room No.')</th>
                                <th>@lang('Room Type')</th>
                                <th>@lang('Fare')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($bookedRooms as $key => $item)
                                <tr class="custom-table__subhead">
                                    <td colspan="3" style="text-align: center;">
                                        {{ __(showDateTime($key, 'd M, Y')) }}
                                    </td>
                                </tr>
                                @foreach ($item as $booked)
                                    <tr>
                                        <td class="text-start">{{ __($booked->room->room_number) }}
                                            @if ($booked->status == Status::ROOM_CANCELED)
                                                - @lang('Canceled')
                                            @endif
                                        </td>
                                        <td>{{ __($booked->room->roomType->name) }}</td>
                                        <td>{{ __(showAmount($booked->fare)) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach

                            <tr class="custom-table__subhead">
                                <td class="text-end" colspan="2">@lang('Total Fare')</td>
                                <td>{{ showAmount($totalFare) }}</td>
                            </tr>

                            @if (!$extraService)
                                @include('partials.invoice_calculation_summary')
                            @endif
                        </tbody>
                    </table>

                    @if ($extraService)
                        @php
                            $extraServices = $booking->usedExtraService->groupBy('service_date');
                        @endphp
                        <div class="extra-service">
                            <div class="mt-10">
                                <h5 class="title">@lang('Service Details')</h5>
                            </div>
                            <table class="table-bordered custom-table table">
                                <thead>
                                    <tr>
                                        <th>@lang('Room No.')</th>
                                        <th>@lang('Service')</th>
                                        <th>@lang('Quantity')</th>
                                        <th>@lang('Unit Price')</th>
                                        <th>@lang('Amount')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($extraServices as $key => $serviceItems)
                                        <tr class="custom-table__subhead">
                                            <td colspan="5" style="text-align: center;">{{ __(showDateTime($key, 'd M, Y')) }}</td>
                                        </tr>
                                        @foreach ($serviceItems as $service)
                                            <tr>
                                                <td>{{ __($service->room->room_number) }}</td>
                                                <td>{{ __($service->extraService->name) }}</td>
                                                <td>{{ $service->qty }}</td>
                                                <td>{{ showAmount($service->unit_price) }}</td>
                                                <td>{{ showAmount($service->total_amount) }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    <tr class="custom-table__subhead">
                                        <td class="text-end" colspan="4">@lang('Total Charge')</td>
                                        <td>{{ showAmount($booking->service_cost) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="summary avoid_page_break">
                            <div class="mt-10">
                                <h5 class="title">@lang('Billing Details')</h5>
                            </div>
                            <table class="table-bordered custom-table table">
                                <tbody>
                                    @include('partials.invoice_calculation_summary')
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>

</html>
