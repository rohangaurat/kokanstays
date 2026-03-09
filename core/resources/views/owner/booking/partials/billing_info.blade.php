<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title">@lang('Payment Info')</h5>
            @can('owner.booking.details')
                <a class="btn btn-sm btn--primary" href="{{ route('owner.booking.details', $booking->id) }}"> <i class="las la-desktop"></i>@lang('View Details')</a>
            @endcan
        </div>
        <div class="list">
            <div class="list-item">
                <span>@lang('Total Fare')</span>
                <span class="text-end">+{{ showAmount($totalFare) }}</span>
            </div>

            @if ($booking->total_discount > 0)
                <div class="list-item">
                    <span>@lang('Discount')</span>
                    <span class="text-end"> -{{ showAmount($booking->total_discount) }}</span>
                </div>
            @endif

            <div class="list-item">
                <span>{{ __(hotelSetting('tax_name')) }} @lang('Charge')</span>
                <span class="text-end">+{{ showAmount($totalTaxCharge) }}</span>
            </div>

            <div class="list-item">
                <span>@lang('Canceled Fare')</span>
                <span class="text-end">-{{ showAmount($canceledFare) }}</span>
            </div>

            <div class="list-item">
                <span>@lang('Canceled') {{ __(hotelSetting('tax_name')) }} @lang('Charge')</span>
                <span class="text-end">-{{ showAmount($canceledTaxCharge) }}</span>
            </div>

            <div class="list-item">
                <span>@lang('Extra Service Charge')</span>
                <span class="text-end">+{{ showAmount($booking->service_cost) }}</span>
            </div>

            <div class="list-item">
                <span>@lang('Other Charges')</span>
                <span class="text-end">+{{ showAmount($booking->extraCharge()) }}</span>
            </div>

            <div class="list-item">
                <span>@lang('Cancellation Fee')</span>
                <span class="text-end">+{{ showAmount($booking->cancellation_fee) }}</span>
            </div>

            <div class="list-item">
                <span class="fw-bold">@lang('Total Amount')</span>
                <span class="fw-bold text-end"> = {{ showAmount($booking->total_amount) }}</span>
            </div>

        </div>
    </div>
</div>
