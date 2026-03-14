@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        @if (request()->routeIs('admin.deposit.list') || request()->routeIs('admin.deposit.method'))
            <div class="col-12">
                @include('admin.deposit.widget')
            </div>
        @endif
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Gateway | Transaction')</th>
                                    <th>@lang('Initiated')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Conversion')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    @php
                                        $details = $deposit->detail ? json_encode($deposit->detail) : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-bold">

@if ($deposit->method_code == 0)

    <span class="text--primary">@lang('Wallet')</span>

@elseif ($deposit->method_code < 5000)

    <a href="{{ appendQuery('method', @$deposit->gateway->alias) }}">
        {{ __(@$deposit->gateway->name) }}
    </a>

@else

    <a href="{{ appendQuery('method', $deposit->method_code) }}">
        @lang('Google Pay')
    </a>

@endif

</span>
                                            <br>
                                            <small> {{ $deposit->trx }} </small>
                                        </td>

                                        <td>
                                            {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                        </td>
                                        <td>
                                            @if ($deposit->user_id)
                                                <a
                                                    href="{{ appendQuery('search', @$deposit->user->username) }}"><span>@</span>{{ $deposit->user->username }}</a><br>
                                                <small class="text-muted">@lang('User')</small>
                                            @elseif($deposit->owner_id)
                                                <a
                                                    href="{{ appendQuery('search', @$deposit->owner->firstname) }}">{{ $deposit->owner->fullname }}</a>
                                                <br>
                                                <small class="text-muted">@lang('Vendor')</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showAmount($deposit->amount) }} + <span class="text--danger"
                                                title="@lang('charge')">{{ showAmount($deposit->charge) }} </span>
                                            <br>
                                            <strong title="@lang('Amount with charge')">
                                                {{ showAmount($deposit->amount + $deposit->charge) }}
                                            </strong>
                                        </td>
                                        <td>
                                            {{ showAmount(1) }} = {{ showAmount($deposit->rate, currencyFormat: false) }}
                                            {{ __($deposit->method_currency) }}
                                            <br>
                                            <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                                {{ __($deposit->method_currency) }}</strong>
                                        </td>
                                        <td>
                                            @php echo $deposit->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.deposit.details', $deposit->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-desktop"></i> @lang('Details')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($deposits->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($deposits) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch='yes' placeholder='Username / TRX' />
    <div class="input-group w-auto flex-fill">
        <select class="from-control bg--white select2" data-minimum-results-for-search="-1" form="searchForm"
            name="payment_by">
            <option value="">@lang('All Deposits')</option>
            <option @selected(request()->payment_by == 'user_id') value="user_id">@lang('User Deposits')</option>
            <option @selected(request()->payment_by == 'owner_id') value="owner_id">@lang('Vendor Deposits')</option>
        </select>
    </div>
@endpush

@push('script')
    <script>
        "use strict";

        $('[name=payment_by]').on('change', function() {
            $('#searchForm').submit();
        });
    </script>
@endpush

@push('style')
    <style>
        .select2-container--default .select2-selection--single {
            width: 200px !important;
        }
    </style>
@endpush
