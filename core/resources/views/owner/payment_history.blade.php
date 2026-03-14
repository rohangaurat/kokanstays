@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Gateway | Transaction')</th>
                                    <th>@lang('Initiated At')</th>
                                    <th>@lang('Pay For')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Conversion')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>
                                            <span class="fw-bold"> <span class="text-primary">{{ $deposit->method_code == 0 ? 'Wallet' : __($deposit->gateway?->name) }}</span> </span>
                                            <br>
                                            <small> {{ $deposit->trx }} </small>
                                        </td>
                                        <td>{{ showDateTime($deposit->created_at) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $deposit->pay_for_month }} {{ Str::plural('month', $deposit->pay_for_month) }}</span>
                                        </td>
                                        <td>
                                            {{ showAmount($deposit->amount) }} + <span class="text-danger" data-bs-toggle="tooltip" title="@lang('Charge')">{{ showAmount($deposit->charge) }} </span>
                                            <br>
                                            <strong data-bs-toggle="tooltip" title="@lang('Amount with charge')">
                                                {{ showAmount($deposit->amount + $deposit->charge) }}
                                            </strong>
                                        </td>
                                        <td>
                                            1 {{ __(gs()->cur_text) }} = {{ showAmount($deposit->rate, currencyFormat:false) }} {{ __($deposit->method_currency) }}
                                            <br>
                                            <strong>{{ showAmount($deposit->final_amount, currencyFormat:false) }} {{ __($deposit->method_currency) }}</strong>
                                        </td>
                                        <td>@php echo $deposit->statusBadge; @endphp</td>
@php
if ($deposit->method_code == 0) {
    $details = [
        [
            'name' => 'Payment Method',
            'value' => 'Wallet'
        ],
        [
            'name' => 'Subscription',
            'value' => $deposit->pay_for_month . ' ' . Str::plural('month', $deposit->pay_for_month)
        ],
        [
            'name' => 'Transaction ID',
            'value' => $deposit->trx
        ],
        [
            'name' => 'Amount',
            'value' => showAmount($deposit->amount)
        ],
        [
            'name' => 'Status',
            'value' => 'Success'
        ]
    ];
} else {
    $details = $deposit->detail ?? [];
}
@endphp
                                        <td>
                                            <div class="button--group">
                                                <button data-info='{{ json_encode($details, JSON_HEX_APOS) }}' @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif class="btn btn-sm btn-outline--primary detailBtn" title="@lang('Detail')"
                                                    type="button">
                                                    <i class="la la-desktop"></i>@lang('Detail')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($deposits->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($deposits) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div class="modal fade" id="detailModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush userData mb-2 p-0">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--dark btn-sm" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch="yes" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
    var modal = $('#detailModal');

    var userData = $(this).data('info');

    if (typeof userData === "string") {
        userData = JSON.parse(userData);
    }

    var html = '';

    if (userData) {
        userData.forEach(element => {
            html += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>${element.name}</span>
                <span>${element.value}</span>
            </li>`;
        });
    }

    modal.find('.userData').html(html);

    if ($(this).data('admin_feedback') != undefined) {
        var ownerFeedback = `
            <div class="my-3">
                <strong>Admin Feedback</strong>
                <p>${$(this).data('admin_feedback')}</p>
            </div>
        `;
    } else {
        var ownerFeedback = '';
    }

    modal.find('.feedback').html(ownerFeedback);

    modal.modal('show');
});
        })(jQuery);
    </script>
@endpush
