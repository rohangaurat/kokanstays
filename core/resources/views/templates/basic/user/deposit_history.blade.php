@extends('Template::layouts.master')
@section('content')
    <div class="custom--card card mb-3">
        <div class="card-header">
            <div class="card-header__wrapper">
                <div>
                    <h5 class="profile-wrapper__title">{{ __($pageTitle) }}</h5>
                    <p class="profile-wrapper__text mb-0">
                        @lang('View your complete booking payment history quickly and easily anytime.')
                    </p>
                </div>
                <form>
                    <div class="search-form">
                        <input type="text" name="search" value="{{ request('search') }}" class="form--control form-two" placeholder="@lang('Search...')">
                        <button class="search-form__icon"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="custom--card card">
        <div class="card-body p-0">
            @if (!blank($deposits))
                <div class="accordion table--acordion custom--accordion" id="transactionAccordion">
                    @foreach ($deposits as $deposit)
                        <div class="accordion-item transaction-item">
                            <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#c-{{ $loop->iteration }}">
                                    <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                        <div class="left">
                                            <div class="content p-0">
                                                <h6 class="trans-title">
                                                    @if ($deposit->method_code == 0)
    {{ $deposit->detail ?? __('Paid at Hotel') }}
@elseif ($deposit->method_code < 5000)
    {{ __($deposit->gateway->name ?? '') }}
@else
    @lang('Google Pay')
@endif
                                                </h6>
                                                <span class="text-muted fs-14 mt-2">#{{ $deposit->trx }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                                        <p class="text-muted fs-14 text-start">
                                            {{ showDateTime($deposit->created_at, 'M d Y @g:i:a') }}
                                            <br>
                                            {{ diffForHumans($deposit->created_at) }}
                                        </p>
                                    </div>
                                    <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                        <p><b>{{ showAmount($deposit->amount) }}</b></p>
                                    </div>
                                </button>
                            </h2>
                            <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                                data-bs-parent="#transactionAccordion">
                                <div class="accordion-body">
                                    <ul class="caption-list">
                                        <li>
                                            <span class="caption">@lang('Charge')</span>
                                            <span class="value">{{ showAmount($deposit->charge) }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Final Amount')</span>
                                            <span class="value">
                                                {{ showAmount($deposit->amount) }}
                                                +
                                                <span class="text--danger" data-bs-toggle="tooltip"
                                                    title="@lang('Processing Charge')">
                                                    {{ showAmount($deposit->charge) }}
                                                </span>
                                                =
                                                <strong data-bs-toggle="tooltip" title="@lang('Amount with charge')">
                                                    {{ showAmount($deposit->amount + $deposit->charge) }}
                                                </strong>
                                            </span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Conversion Rate')</span>
                                            <span class="value">
                                                {{ showAmount(1) }} =
                                                {{ showAmount($deposit->rate, currencyFormat: false) }}
                                                {{ __($deposit->method_currency) }}
                                            </span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('After Conversion')</span>
                                            <span class="value">
                                                {{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                                {{ __($deposit->method_currency) }}
                                            </span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Status')</span>
                                            <span class="value">@php echo $deposit->statusBadge @endphp</span>
                                        </li>
                                        @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                            @foreach ($deposit->detail as $detail)
                                                <li>
                                                    <span class="caption">{{ __($detail->name) }}</span>
                                                    <span class="value">
                                                        @if ($detail->type == 'file')
                                                            <a
                                                                href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $detail->value)) }}">
                                                                <i class="fa-regular fa-file"></i>
                                                                @lang('Attachment')
                                                            </a>
                                                        @else
                                                            {{ __($detail->value) }}
                                                        @endif
                                                    </span>
                                                </li>
                                            @endforeach
                                            @if ($deposit->status == Status::PAYMENT_REJECT)
                                                <li>
                                                    <span class="caption">@lang('Admin Feedback')</span>
                                                    <span class="value">{{ $deposit->admin_feedback }}</span>
                                                </li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @include('Template::partials.empty_list', ['message' => 'No payment found.'])
            @endif
        </div>
    </div>

    @if ($deposits->hasPages())
        <div class="mt-4">{{ paginateLinks($deposits) }}</div>
    @endif
@endsection
