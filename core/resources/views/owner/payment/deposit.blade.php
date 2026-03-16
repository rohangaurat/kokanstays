@extends('owner.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
            <div class="card bl--5-primary mb-3">
                <div class="card-body">
                    <form action="{{ route('owner.update.auto.payment.status') }}" class="autoPaymentForm" method="POST">
                        @csrf
                        <div class="d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0">@lang('Auto Payment')</p>
                                <p class="mb-0">
                                    <small>@lang('If auto-payment is enabled, your monthly payment is handled automatically by the system.')</small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input @if (authOwner()->auto_payment) checked @endif data-bs-toggle="toggle"
                                    data-height="35" data-off="@lang('Off')" data-offstyle="-danger"
                                    data-on="@lang('On')" data-onstyle="-success" data-size="large" data-width="100%"
                                    name="auto_payment" type="checkbox">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <form action="{{ route('owner.deposit.insert') }}" method="post">
                @csrf
                <input name="currency" type="hidden" value="{{ gs('cur_text') }}">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Subscription Payment')</h5>
                        <p class="mb-0">
                            <small class="">@lang('You can pay for maximum') <span class="fw-bold">{{ gs()->maximum_payment_month }}
                                    @lang('months').</span>
                                <i class="text--warning">@lang('Your subscription expired on')
                                    {{ showDateTime(authOwner()->expire_at, 'd M,Y') }}.</i>
                            </small>
                        </p>
                    </div>
                    <div class="card-body">
                        @if ($pendingPayment)
                            <div class="alert alert-info p-3">
                                <p>@lang('You have a pending payment of') {{ showAmount($pendingPayment->amount) }} @lang('for')
                                    {{ $pendingPayment->pay_for_month }} @lang('months. Please wait for super admin response. You can see your payment history by click') <a
                                        href="{{ route('owner.payment.history') }}">@lang('here...')</a></p>
                            </div>
                        @endif
                        <div class="d-flex justify-content-center flex-wrap gap-2 my-3">
                            @foreach ([12,24] as $months)
                        <div class="paying_month {{ $months == 12 ? 'selected' : '' }}" data-pay_for="{{ $months }}">
                        <span class="title">@lang('Pay For') {{ $months }} @lang('Months')</span>
                        </div>
                        @endforeach
                        </div>
                        <div class="form-group">
                            <label>@lang('Pay For')</label>
                            <div class="input-group">
                                <input type="number" min="12" max="24" step="12"
class="form-control" name="pay_for_month" value="12" required>
                                <span class="input-group-text">@lang('Months')</span>
                            </div>
                        </div>
                        <div class="form-group select2-parent position-relative">
                            <label>@lang('Payment Via')</label>
                            <select class="form-control select2-gateway" name="gateway" required>
                                <option data-title="@lang('Select One')" value="">@lang('Select One')</option>

                                <option value="-1" data-charge="{{ gs()->cur_sym . '0.00' }}"
                                    data-title="@lang('Wallet Balance') ({{ showAmount(authOwner()->balance) }})">
                                    @lang('Wallet Balance')</option>
                                @foreach ($gatewayCurrency as $data)
                                    <option data-gateway="{{ $data }}" data-title="{{ __($data->name) }}"
                                        data-img_url="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                        data-min_amount="{{ gs()->cur_sym . showAmount($data->min_amount, currencyFormat: false) }}"
                                        data-max_amount="{{ gs()->cur_sym . showAmount($data->max_amount, currencyFormat: false) }}"
                                        data-charge="{{ showAmount($data->fixed_charge) }} + {{ getAmount($data->percent_charge) }}%"
                                        value="{{ $data->method_code }}" @selected(old('gateway') == $data->method_code)>
                                        {{ __($data->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Per Month Bill')</span>
                                <span class="fw-bold">{{ showAmount(gs()->bill_per_month) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Charge')</span>
                                <span class="fw-bold"><span class="charge">0</span> {{ __(gs()->cur_text) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Payable')</span>
                                <span class="fw-bold"><span class="payable">0</span> {{ __(gs()->cur_text) }}</span>
                            </li>
                            <li class="list-group-item justify-content-between d-none rate-element px-0"></li>
                            <li class="list-group-item justify-content-between d-none in-site-cur px-0">
                                <span>@lang('In') <span class="base-currency"></span></span>
                                <span class="finalAmount fw-bold">0</span>
                            </li>
                            <li class="list-group-item justify-content-center crypto_currency d-none px-0">
                                <span>@lang('Conversion with') <span class="method_currency"></span> @lang('and final value will Show on next step')</span>
                            </li>
                        </ul>
                        <button class="btn btn--primary h-45 w-100 mt-3" type="submit">@lang('Submit')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.payment.history') }}" class="btn btn-sm btn--primary">
        <i class="las la-list"></i>@lang('View History')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            $(document).ready(function () {
    $('.paying_month.selected').trigger('click');
});
            "use strict";

            function formatState(state) {
                let element = $(state.element);
                if (!element.data('img_url')) {
                    return $(`<span class="gateway-option">
                            <span class="title">${element.data('title')}</span>
                        </span>`);
                }

                return $(`<span class="gateway-option"><img src="${element.data('img_url')}">
                        <span class="title_wrapper">
                            <span class="title">${element.data('title')} (${element.data('min_amount')} - ${element.data('max_amount')})</span>
                            <span class="charge">@lang('Charge'): ${element.data('charge')}</span>
                        </span>
                    </span>`);
            };

            $('.select2-gateway').select2({
                dropdownParent: $('.select2-parent'),
                templateResult: formatState
            });

            $('.paying_month').on('click', function() {

    $('.paying_month').removeClass('selected');
    $(this).addClass('selected');

    let month = $(this).data('pay_for');
    $('[name=pay_for_month]').val(month).trigger("change");

});

            $('[name=pay_for_month]').on('input change', function() {
                var payFor = Number($(this).val());
                if (payFor != 12 && payFor != 24) {
                notify('error', 'You can pay only for 12 or 24 months');
                $('[name=pay_for_month]').val('');
                return;
            }
                calculate();
            });

            $('select[name=gateway]').on('change', function() {
                if (!$('select[name=gateway]').val()) {
                    $('.preview-details').addClass('d-none');
                }
                if ($('[name=pay_for_month]').val() == '') {
                    return false;
                }
                calculate();
            });

            function calculate() {
                let siteCurrency = `{{ gs('cur_text') }}`;
                if ($('select[name=gateway] option:selected').val() == -1) {
                    $('.list-group').addClass('d-none');
                    $('input[name=currency]').val(siteCurrency);
                    return;
                } else {
                    $('.list-group').removeClass('d-none');
                }

                let fixedCharge = 0;
                let percentCharge = 0;
                let rate = 1;
                let resourceCurrency = `{{ gs('cur_text') }}`;

                var resource = $('select[name=gateway] option:selected').data('gateway');
                if (resource != undefined) {
                    fixedCharge = parseFloat(resource.fixed_charge);
                    percentCharge = parseFloat(resource.percent_charge);
                    rate = parseFloat(resource.rate)
                    resourceCurrency = resource.currency;

                    if (resource.method.crypto == 1) {
                        var toFixedDigit = 8;
                        $('.crypto_currency').removeClass('d-none');
                    } else {
                        var toFixedDigit = 2;
                        $('.crypto_currency').addClass('d-none');
                    }
                }


                var payFor = $('[name=pay_for_month]').val() * 1;
                var billPerMonth = @json(gs('bill_per_month'));
                var amount = payFor * billPerMonth;

                if (amount <= 0) {
                    $('.preview-details').addClass('d-none');
                }
                $('.preview-details').removeClass('d-none');

                let charge = 0;
                if (payFor > 0) {
                    charge = parseFloat(fixedCharge + (amount * percentCharge / 100)).toFixed(2);
                }
                var payable = parseFloat((parseFloat(amount) + parseFloat(charge))).toFixed(2);
                var finalAmount = (parseFloat((parseFloat(amount) + parseFloat(charge))) * rate).toFixed(toFixedDigit);

                $('.charge').text(charge);
                $('.payable').text(payable);
                $('.finalAmount').text(finalAmount);

                if (resource != undefined && resourceCurrency != siteCurrency) {
                    var rateElement =
                        `<span class="fw-bold">@lang('Conversion Rate')</span> <span><span  class="fw-bold">1 ${siteCurrency} = <span class="rate">${rate}</span>  <span class="method_currency"> ${resourceCurrency}</span></span></span>`;
                    $('.rate-element').html(rateElement)
                    $('.rate-element').removeClass('d-none');
                    $('.in-site-cur').removeClass('d-none');
                    $('.rate-element').addClass('d-flex');
                    $('.in-site-cur').addClass('d-flex');
                } else {
                    $('.rate-element').html('')
                    $('.rate-element').addClass('d-none');
                    $('.in-site-cur').addClass('d-none');
                    $('.rate-element').removeClass('d-flex');
                    $('.in-site-cur').removeClass('d-flex');
                }
                $('.method_currency').text(resourceCurrency);
                $('input[name=currency]').val(resourceCurrency);
            }

            $('[name=auto_payment]').on('change', function() {
                $('.autoPaymentForm').submit();
            })
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .paying_month {
            border: 1px solid #ebebeb;
            padding: 20px 30px;
            border-radius: 10px;
            cursor: pointer;
            background: #ebebeb75;
        }

        .paying_month.selected {
            background-color: #4634ff;
        }

        .paying_month.selected .title {
            color: #fff;
        }

        .gateway-option {
            display: flex;
            justify-content: start;
            align-items: center;
        }

        .gateway-option img {
            height: 40px;
        }

        .gateway-option .title_wrapper {
            margin-left: 10px;
            display: flex;
            flex-direction: column;
        }

        .gateway-option .title {
            font-weight: 600;
            font-size: 13px;
        }

        .gateway-option .charge {
            font-size: 12px;
        }
    </style>
@endpush
