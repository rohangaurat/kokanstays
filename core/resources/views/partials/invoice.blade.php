<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{{ gs()->site_name }} - Invoice</title>

<link rel="shortcut icon" href="{{ siteFavicon() }}" type="image/png">
<link rel="stylesheet" href="{{ asset('assets/owner/css/invoice.css') }}">

<style>
body{
font-family: DejaVu Sans, sans-serif;
font-size:13px;
color:#333;
}

table{
width:100%;
border-collapse:collapse;
}

th, td{
padding:8px;
}

thead{
background:#f5f5f5;
}

.section-title{
margin-top:25px;
margin-bottom:10px;
font-weight:bold;
font-size:16px;
}

.header-table td{
vertical-align:top;
}

.text-right{
text-align:right;
}

.footer{
margin-top:40px;
text-align:center;
font-size:12px;
color:#777;
}
</style>

</head>

<body>

@php
$extraService = count($booking->usedExtraService);

$due = max(0, $booking->total_amount - $booking->paid_amount);

$bookedRooms = $booking->bookedRooms->groupBy('booked_for');

$totalFare = $booking->bookedRooms->sum('fare');

$totalTaxCharge = $booking->bookedRooms->sum('tax_charge');

$canceledFare = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('fare');

$canceledTaxCharge = $booking->bookedRooms->where('status', Status::ROOM_CANCELED)->sum('tax_charge');

$payments = $booking->payments->where('type', 'BOOKING_PAYMENT_RECEIVED');

$refunds = $booking->payments->where('type', 'BOOKING_PAYMENT_RETURNED');
@endphp


{{-- HEADER --}}
<table class="header-table">
<tr>

<td style="width:50%">
<img src="{{ siteLogo('dark') }}" style="height:60px;">
</td>

<td class="text-right" style="width:50%">
<h2 style="margin:0;">@lang('INVOICE')</h2>
<span>{{ date('d M Y') }}</span>
</td>

</tr>
</table>


{{-- CUSTOMER + BILL INFO --}}
<table style="margin-top:25px">
<tr>

<td style="width:50%">
<h4>@lang('Invoice To')</h4>

<p>
<strong>@lang('Name'):</strong>
{{ $booking->user ? $booking->user->fullname : $booking->guest->name }}<br>

<strong>@lang('Email'):</strong>
{{ $booking->user ? $booking->user->email : $booking->guest->email }}<br>

<strong>@lang('Mobile'):</strong>
+{{ $booking->user ? $booking->user->mobile : $booking->guest->mobile }}
</p>
</td>


<td style="width:50%" class="text-right">
<h4>@lang('Bill Information')</h4>

<p>
<strong>@lang('Booking No'):</strong>
{{ $booking->booking_number }}<br>

<strong>@lang('Booking Date'):</strong>
{{ showDateTime($booking->created_at) }}<br>

<strong>@lang('Total Amount'):</strong>
{{ showAmount($booking->total_amount) }}<br>

<strong>@lang('Paid Amount'):</strong>
{{ showAmount($booking->paid_amount) }}
</p>
</td>

</tr>
</table>


{{-- PAYMENTS RECEIVED --}}
@if($payments->count())

<div class="section-title">@lang('Payments Received')</div>

<table class="table-bordered custom-table">
<thead>
<tr>
<th>@lang('Date')</th>
<th>@lang('Payment Method')</th>
<th>@lang('Amount')</th>
</tr>
</thead>

<tbody>

@foreach($payments as $payment)

<tr>

<td>
{{ showDateTime($payment->created_at,'d M Y H:i') }}
</td>

<td>

{{ $payment->payment_system }}

@if($payment->owner_id > 0)
<br>
<small>Paid directly to Hotel</small>
@else
<br>
<small>Paid to KokanStays Platform</small>
@endif

</td>

<td>
{{ showAmount($payment->amount) }}
</td>

</tr>

@endforeach

</tbody>
</table>

@endif



{{-- PAYMENTS RETURNED --}}
@if($refunds->count())

<div class="section-title">@lang('Payments Returned')</div>

<table class="table-bordered custom-table">

<thead>
<tr>
<th>@lang('Date')</th>
<th>@lang('Payment Method')</th>
<th>@lang('Amount')</th>
</tr>
</thead>

<tbody>

@foreach($refunds as $refund)

<tr>

<td>
{{ showDateTime($refund->created_at,'d M Y H:i') }}
</td>

<td>
{{ $refund->payment_system }}
</td>

<td>
{{ showAmount($refund->amount) }}
</td>

</tr>

@endforeach

</tbody>

</table>

@endif



{{-- ROOM DETAILS --}}
<div class="section-title">@lang("Room Details")</div>

<table class="table-bordered custom-table">

<thead>
<tr>
<th>@lang('Room No.')</th>
<th>@lang('Room Type')</th>
<th>@lang('Fare')</th>
</tr>
</thead>

<tbody>

@foreach ($bookedRooms as $key => $item)

<tr style="background:#fafafa">
<td colspan="3" style="text-align:center">
{{ showDateTime($key,'d M Y') }}
</td>
</tr>

@foreach ($item as $booked)

<tr>

<td>
{{ $booked->room->room_number }}

@if ($booked->status == Status::ROOM_CANCELED)
- @lang('Canceled')
@endif
</td>

<td>
{{ $booked->room->roomType->name }}
</td>

<td>
{{ showAmount($booked->fare) }}
</td>

</tr>

@endforeach

@endforeach


<tr style="background:#fafafa">
<td colspan="2" class="text-right">
<strong>@lang('Total Fare')</strong>
</td>

<td>
{{ showAmount($totalFare) }}
</td>
</tr>


@if(!$extraService)

@include('partials.invoice_calculation_summary')

@endif


</tbody>
</table>



{{-- EXTRA SERVICES --}}
@if ($extraService)

@php
$extraServices = $booking->usedExtraService->groupBy('service_date');
@endphp

<div class="section-title">@lang('Service Details')</div>

<table class="table-bordered custom-table">

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

<tr style="background:#fafafa">
<td colspan="5" style="text-align:center">
{{ showDateTime($key,'d M Y') }}
</td>
</tr>

@foreach ($serviceItems as $service)

<tr>

<td>{{ $service->room->room_number }}</td>

<td>{{ $service->extraService->name }}</td>

<td>{{ $service->qty }}</td>

<td>{{ showAmount($service->unit_price) }}</td>

<td>{{ showAmount($service->total_amount) }}</td>

</tr>

@endforeach

@endforeach

<tr style="background:#fafafa">
<td colspan="4" class="text-right">
<strong>@lang('Total Charge')</strong>
</td>

<td>
{{ showAmount($booking->service_cost) }}
</td>
</tr>

</tbody>

</table>



<div class="section-title">@lang('Billing Summary')</div>

<table class="table-bordered custom-table">

<tbody>

@include('partials.invoice_calculation_summary')

</tbody>

</table>

@endif



{{-- FOOTER --}}
<div class="footer">
<hr>
<p>
@lang('Thank you for choosing') {{ gs()->site_name }}
</p>
</div>


</body>
</html>