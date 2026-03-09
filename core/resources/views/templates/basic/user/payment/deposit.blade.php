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
                                <div class="col-md-6 col-xl-6">
                                    <div class="payment-system-list is-scrollable gateway-option-list">
                                        @foreach ($gatewayCurrency as $data)
                                            <label for="{{ titleToKey($data->name) }}"
                                                class="payment-item {{ $loop->index > 4 ? 'd-none' : '' }} gateway-option">
                                                <span class="payment-check d-none">
                                                    <input class="payment-item__radio gateway-input" type="radio"
                                                        value="{{ $data->method_code }}" name="gateway"
                                                        id="{{ titleToKey($data->name) }}"
                                                        data-gateway='@json($data)'
                                                        data-min-amount="{{ showAmount($data->min_amount) }}"
                                                        data-max-amount="{{ showAmount($data->max_amount) }}"
                                                        @checked(old('gateway', $loop->first) == $data->method_code)>
                                                </span>
                                                <span class="payment-item__right">
                                                    <span class="payment-item__info">
                                                        <span class="payment-item__check"></span>
                                                        <span class="payment-item__name">{{ __($data->name) }}</span>
                                                    </span>
                                                    <span class="payment-item__thumb">
                                                        <img class="payment-item__thumb-img"
                                                            src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                                            alt="@lang('payment-thumb')">
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                        @if ($gatewayCurrency->count() > 4)
                                            <button type="button" class="payment-item__btn more-gateway-option">
                                                @lang('See More')
                                                <span class="icon"><i class="la la-arrow-down"></i></span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-6">
                                    <div class="payment-system-list">
                                        <div class="deposit-info">
                                            <div class="deposit-info__title">
                                                <p class="text mb-0">@lang('Amount')</p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <div class="deposit-info__input-group input-group">
                                                    <span class="deposit-info__input-group-text">{{ gs('cur_sym') }}</span>
                                                    <input type="text" class="form-control form--control amount"
                                                        name="amount" placeholder="@lang('00.00')" autocomplete="off"
                                                        value="{{ old('amount', abs($booking->due_amount)) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="deposit-info">
                                            <div class="deposit-info__title">
                                                <p class="text has-icon"> @lang('Limit')</p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text"><span class="gateway-limit">@lang('0.00')</span> </p>
                                            </div>
                                        </div>
                                        <div class="deposit-info">
                                            <div class="deposit-info__title">
                                                <p class="text has-icon">@lang('Charge')
                                                    <span data-bs-toggle="tooltip" title="@lang('Processing charge for payment method')"
                                                        class="processing-fee-info">
                                                        <i class="las la-info-circle"></i>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text">
                                                    {{ gs('cur_sym') }}<span
                                                        class="processing-fee">@lang('0.00')</span>
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
                                                    {{ gs('cur_sym') }}<span class="final-amount">@lang('0.00')</span>
                                                    {{ __(gs('cur_text')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="deposit-info gateway-conversion d-none total-amount pt-1">
                                            <div class="deposit-info__title">
                                                <p class="text">@lang('Conversion')</p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text"></p>
                                            </div>
                                        </div>
                                        <div class="deposit-info conversion-currency d-none total-amount pt-1">
                                            <div class="deposit-info__title">
                                                <p class="text">
                                                    @lang('In') <span class="gateway-currency"></span>
                                                </p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text"><span class="in-currency"></span></p>
                                            </div>
                                        </div>
                                        <br>
                                        <button type="submit" class="btn btn--base btn--lg w-100" disabled>
                                            @lang('Confirm Payment')
                                        </button>
                                        <div class="info-text pt-3">
                                            <p class="text">
                                                @lang('Ensure your payment is secure with our world-class payment options and a trusted payment process.')
                                            </p>
                                        </div>
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
        (function($) {
            "use strict";

            var amount = parseFloat($('.amount').val() || 0);
            var gateway, minAmount, maxAmount;

            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());
                if (!amount) amount = 0;
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');
                minAmount = gatewayElement.data('min-amount');
                maxAmount = gatewayElement.data('max-amount');

                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
                $(".processing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                calculation();
            }

            gatewayChange();

            $(".more-gateway-option").on("click", function(e) {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            function calculation() {
                if (!gateway) return;
                $(".gateway-limit").text(minAmount + " - " + maxAmount);

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height')

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ?
                        8 : 2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.deposit-form').removeClass('adjust-height')
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            $('.gateway-input').change();
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .gateway-card .payment-system-list {
            --thumb-width: 80px;
            --thumb-height: 32px;
            --radio-size: 20px;
            border-radius: 5px;
            height: 100%;
        }

        .gateway-card .payment-system-list.is-scrollable {
            max-height: min(405px, 70vh);
            overflow-x: auto;
            padding-block: 4px;
            padding-right: 8px;
        }
        @media (max-width: 767px) {
            .gateway-card .payment-system-list.is-scrollable {
                padding-right: 0;
            }
        }
        .gateway-card .payment-system-list::-webkit-scrollbar {
            width: 5px;
        }

        .gateway-card .payment-system-list::-webkit-scrollbar-thumb {
            background-color: hsl(var(--base));
            border-radius: 10px;
        }

        .gateway-card .payment-item {
            width: 100%;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            padding: 16px 16px;
            border: 1px solid hsl(var(--black) / 0.1);
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
            margin-bottom: 8px;
            border-radius: 8px;
            font-size: 0.875rem;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        @media (max-width: 375px) {
            .gateway-card .payment-item {
                padding: 12px 10px;
            }
        }
        .gateway-card .payment-item__info {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
        }

        .gateway-card .payment-item__check {
            width: var(--radio-size);
            height: var(--radio-size);
            border: 1px solid hsl(var(--base));
            display: inline-block;
            border-radius: 100%;
        }

        .gateway-card .payment-item__name {
            padding-left: 10px;
            width: calc(100% - var(--radio-size));
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
            font-weight: 600;
            color: hsl(var(--black) / 0.65);
        }

        .gateway-card .payment-item__thumb {
            -ms-flex-negative: 0;
            flex-shrink: 0;
            text-align: right;
            padding-left: 10px;
            flex-shrink: 0;
        }

        .gateway-card .payment-item__thumb img {
            max-width: var(--thumb-width);
            max-height: var(--thumb-height);
            -o-object-fit: cover;
            object-fit: cover;
            width: 100%;
        }

        .gateway-card .payment-item__thumb:has(.text) {
            width: -webkit-fit-content;
            width: -moz-fit-content;
            width: fit-content;
        }

        .gateway-card .payment-item__btn {
            font-size: 0.875rem;
            color: hsl(var(--success));
            font-weight: 600;
        }

        .gateway-card .payment-item__check {
            border: 1px solid hsl(var(--black) / 0.1);
        }

        .gateway-card .payment-item:has(.payment-item__radio:checked) .payment-item__check {
            border: 4px solid hsl(var(--base));
        }

        .payment-item:has(.payment-item__radio:checked) {
            border: 1px solid hsl(var(--black) / 0.1);
            border-radius: 8px;
        }

        .gateway-card .payment-item__right {
            width: 100%;
            display: flex;
            align-items: center;
        }

        .deposit-info {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            margin-bottom: 5px;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            gap: 12px;
        }

        .deposit-info * {
            font-weight: 500;
            font-size: 0.9375rem;
        }

        .deposit-info__input * {
            font-weight: 300;
        }

        .total-amount {
            border-top: 1px solid hsl(var(--base-two) / 0.08);
        }

        .label-text:last-child {
            min-width: 30px;
            text-align: end;
        }
    </style>
@endpush
