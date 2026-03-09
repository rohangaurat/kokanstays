@extends('Template::layouts.master')
@section('content')
    <div class="show-filter mb-3 text-end">
        <button type="button" class="btn btn--base showFilterBtn btn-sm">
            <i class="las la-filter"></i> @lang('Filter')
        </button>
    </div>
    <div class="profile-wrapper mb-3 responsive-filter-card">
        <form>
            <div class="d-flex flex-wrap gap-4">
                <div class="flex-grow-1">
                    <input type="search" name="search" value="{{ request()->search }}" class="form--control"
                        placeholder="@lang('Transaction Number')">
                </div>
                <div class="flex-grow-1 select2-parent">
                    <select name="trx_type" class="form--control select2" data-minimum-results-for-search="-1">
                        <option value="">@lang('All Types')</option>
                        <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                        <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                    </select>
                </div>
                <div class="flex-grow-1 select2-parent">
                    <select class="form--control select2" data-minimum-results-for-search="-1" name="remark">
                        <option value="">@lang('All Remarks')</option>
                        @foreach ($remarks as $remark)
                            <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>
                                {{ __(keyToTitle($remark->remark)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow-1 align-self-end">
                    <button class="btn btn--base btn--lg w-100">
                        <i class="las la-filter"></i> @lang('Filter')
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="custom--card card">
        <div class="card-body p-0">
            @if (!blank($transactions))
                <div class="accordion table--acordion custom--accordion" id="transactionAccordion">
                    @foreach ($transactions as $transaction)
                        <div class="accordion-item transaction-item">
                            <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#c-{{ $loop->iteration }}">
                                    <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                        <div class="left">
                                            <div
                                                class="icon tr-icon @if ($transaction->trx_type == '+') icon-success @else icon-danger @endif">
                                                <i class="las la-long-arrow-alt-right"></i>
                                            </div>
                                            <div class="content">
                                                <h6 class="trans-title">{{ __(keyToTitle($transaction->remark)) }}</h6>
                                                <span class="text-muted fs-14 mt-2">
                                                    {{ showDateTime($transaction->created_at, 'M d Y @g:i:a') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                                        <p class="text-muted fs-14"><b>#{{ $transaction->trx }}</b></p>
                                    </div>
                                    <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                        <p>
                                            <b>{{ showAmount($transaction->amount) }}</b>
                                        </p>
                                    </div>
                                </button>
                            </h2>
                            <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                                data-bs-parent="#transactionAccordion">
                                <div class="accordion-body">
                                    <ul class="caption-list">
                                        <li>
                                            <span class="caption">@lang('Charge')</span>
                                            <span class="value">{{ showAmount($transaction->charge) }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Final Amount')</span>
                                            <span class="value">
                                                {{ showAmount($transaction->charge + $transaction->amount) }}
                                            </span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Details')</span>
                                            <span class="value">{{ __($transaction->details) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @include('Template::partials.empty_list', ['message' => 'No transaction found.'])
            @endif
        </div>
    </div>
    @if ($transactions->hasPages())
        <div class="mt-4">{{ paginateLinks($transactions) }}</div>
    @endif
@endsection

@push('style')
    <style>
        @media (max-width: 575px) {
            .transaction-item .content-wrapper {
                padding-left: 2.5rem;
                margin-top: 8px !important;
            }
        }
    </style>
@endpush
