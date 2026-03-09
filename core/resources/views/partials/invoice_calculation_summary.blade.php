@if ($canceledFare > 0)
    <tr class="custom-table__subhead">
        <td class="text-end" colspan="2">@lang('Canceled Fare')</td>
        <td> - {{ showAmount($canceledFare) }}</td>
    </tr>
@endif

@if ($booking->total_discount > 0)
    <tr class="custom-table__subhead">
        <td class="text-end" colspan="2">@lang('Discount')</td>
        <td> - {{ showAmount($booking->total_discount) }}</td>
    </tr>
@endif

@if ($booking->cancellation_fee > 0)
    <tr class="custom-table__subhead">
        <td class="text-end" colspan="2">@lang('Cancellation Fee')</td>
        <td>{{ showAmount($booking->cancellation_fee) }}</td>
    </tr>
@endif
<tr class="custom-table__subhead">
    <td class="text-end" colspan="2">{{ __($booking->owner->hotelSetting->tax_name) }}</td>
    <td> {{ showAmount($totalTaxCharge) }}</td>
</tr>

@if ($canceledTaxCharge > 0)
    <tr class="custom-table__subhead">
        <td class="text-end" colspan="2">
            @lang('Canceled')
            {{ __($booking->owner->hotelSetting->tax_name) }}
            @lang('Charge')</td>
        <td> - {{ showAmount($canceledTaxCharge) }}</td>
    </tr>
@endif

@if ($booking->extraCharge() > 0)
    <tr class="custom-table__subhead">
        <td class="text-end" colspan="2">@lang('Other Charges')</td>
        <td> {{ showAmount($booking->extraCharge()) }}</td>
    </tr>
@endif

<tr class="custom-table__subhead">
    <td class="text-end" colspan="2">@lang('Total')</td>
    <td> = {{ showAmount($booking->total_amount) }}</td>
</tr>

@if ($due > 0)
    <tr class="custom-table__subhead">
        <td class="text-end" colspan="2">@lang('Due')</td>
        <td> = {{ showAmount($due) }}</td>
    </tr>
@elseif($due < 0)
    <tr class="custom-table__subhead">
        <td class="text-end" colspan="2">@lang('Refundable')</td>
        <td> = {{ showAmount(abs($due)) }}</td>
    </tr>
@endif
