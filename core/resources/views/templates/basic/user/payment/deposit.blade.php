@extends('Template::layouts.master')

@section('content')

<div class="booking-main-wrapper">
<div class="row gy-4">

<div class="col-xl-12 col-lg-12">

<div class="payment-system">

<h5 class="title">@lang('Payment Gateway')</h5>

<form action="{{ route('user.deposit.insert') }}" method="post" class="deposit-form">

@csrf

<input type="hidden" name="currency">
<input type="hidden" name="type" value="true">
<input type="hidden" name="booking_id" value="{{ $booking->id }}">

<div class="gateway-card">

<div class="row justify-content-center">

{{-- ================= GATEWAY LIST ================= --}}

<div class="col-md-6 col-xl-6">

<div class="gateway-list">

@foreach ($gatewayCurrency as $data)

<label class="gateway-card-item">

<input
type="radio"
class="gateway-radio gateway-input"
name="gateway"
value="{{ $data->method_code }}"
data-gateway='@json($data)'
data-min-amount="{{ showAmount($data->min_amount) }}"
data-max-amount="{{ showAmount($data->max_amount) }}"
@checked(old('gateway', $loop->first) == $data->method_code)
>

<div class="gateway-content">

<div class="gateway-left">

<span class="radio-circle"></span>

<span class="gateway-name">
{{ __($data->name) }}
</span>

</div>

<div class="gateway-logo">

<img
src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
alt="gateway">

</div>

</div>

</label>

@endforeach

</div>

</div>

{{-- ================= PAYMENT SUMMARY ================= --}}

<div class="col-md-6 col-xl-6">

<div class="payment-system-list">

<div class="deposit-info">

<div class="deposit-info__title">
<p class="text mb-0">@lang('Amount')</p>
</div>

<div class="deposit-info__input">

<div class="deposit-info__input-group input-group">

<span class="deposit-info__input-group-text">{{ gs('cur_sym') }}</span>

<input type="text"
class="form-control form--control amount"
name="amount"
placeholder="@lang('00.00')"
autocomplete="off"
value="{{ old('amount', abs($booking->due_amount)) }}">

</div>

</div>

</div>

<hr>

<div class="deposit-info">

<div class="deposit-info__title">
<p class="text">@lang('Limit')</p>
</div>

<div class="deposit-info__input">
<p class="text">
<span class="gateway-limit">0.00</span>
</p>
</div>

</div>

<div class="deposit-info">

<div class="deposit-info__title">
<p class="text">@lang('Charge')</p>
</div>

<div class="deposit-info__input">
<p class="text">
{{ gs('cur_sym') }}
<span class="processing-fee">0.00</span>
{{ __(gs('cur_text')) }}
</p>
</div>

</div>

<div class="deposit-info total-amount pt-1">

<div class="deposit-info__title">
<p class="text">@lang('Total Payable')</p>
</div>

<div class="deposit-info__input">
<p class="text">
{{ gs('cur_sym') }}
<span class="final-amount">0.00</span>
{{ __(gs('cur_text')) }}
</p>
</div>

</div>

<br>

<button type="submit" class="btn btn--base btn--lg w-100" disabled>
@lang('Confirm Payment')
</button>

</div>

</div>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

@endsection

@push('script')

<script>

(function($){

"use strict";

var amount = parseFloat($('.amount').val() || 0);
var gateway, minAmount, maxAmount;

$('.amount').on('input', function(){

amount = parseFloat($(this).val());

if(!amount) amount = 0;

calculation();

});

$('.gateway-input').on('change', function(){

gatewayChange();

});

function gatewayChange(){

let gatewayElement = $('.gateway-input:checked');

gateway = gatewayElement.data('gateway');
minAmount = gatewayElement.data('min-amount');
maxAmount = gatewayElement.data('max-amount');

calculation();

}

gatewayChange();

function calculation(){

if(!gateway) return;

$(".gateway-limit").text(minAmount + " - " + maxAmount);

let percentCharge = 0;
let fixedCharge = 0;
let totalPercentCharge = 0;

if(amount){

percentCharge = parseFloat(gateway.percent_charge);
fixedCharge = parseFloat(gateway.fixed_charge);

totalPercentCharge = amount / 100 * percentCharge;

}

let totalCharge = totalPercentCharge + fixedCharge;
let totalAmount = (amount || 0) + totalCharge;

$(".processing-fee").text(totalCharge.toFixed(2));
$(".final-amount").text(totalAmount.toFixed(2));

$("input[name=currency]").val(gateway.currency);

if(amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)){

$(".deposit-form button[type=submit]").attr('disabled', true);

}else{

$(".deposit-form button[type=submit]").removeAttr('disabled');

}

}

$('.gateway-input').change();

})(jQuery);

</script>

@endpush

@push('style')

<style>

/* ============================= */
/* PAYMENT GATEWAY MODERN UI */
/* ============================= */

.gateway-list{
display:flex;
flex-direction:column;
gap:12px;
}

/* gateway card */

.gateway-card-item{
border:1px solid #e5e7eb;
border-radius:10px;
padding:16px;
cursor:pointer;
position:relative;
transition:0.2s;
background:#fff;
}

/* hidden radio covering full card */

.gateway-radio{
position:absolute;
inset:0;
opacity:0;
cursor:pointer;
}

/* layout */

.gateway-content{
display:flex;
align-items:center;
justify-content:space-between;
gap:10px;
}

/* left side */

.gateway-left{
display:flex;
align-items:center;
gap:12px;
flex:1;
}

/* radio circle */

.radio-circle{
width:18px;
height:18px;
border-radius:50%;
border:2px solid #ccc;
flex-shrink:0;
}

/* gateway name */

.gateway-name{
font-weight:600;
color:#333;
white-space:nowrap;
overflow:hidden;
text-overflow:ellipsis;
}

/* gateway logo */

.gateway-logo{
flex-shrink:0;
}

.gateway-logo img{
height:28px;
object-fit:contain;
}

/* selected */

.gateway-radio:checked + .gateway-content .radio-circle{
border:6px solid hsl(var(--base));
}

.gateway-card-item:has(.gateway-radio:checked){
border:2px solid hsl(var(--base));
background:hsl(var(--base)/0.05);
}

/* hover */

.gateway-card-item:hover{
border-color:hsl(var(--base));
}

</style>

@endpush