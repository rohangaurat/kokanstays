@extends('owner.layouts.app')
@section('panel')
    @php 
$due = $booking->due_amount;
$refund = $booking->refundable_amount ?? 0;
@endphp

    <div class="row gy-4">
        <div class="col-xxl-6 col-lg-12 col-md-6">
            <div class="row gy-4">
                <div class="col-12">
                    @include('owner.booking.partials.guest_info')
                </div>
                <div class="col-12">
                    @include('owner.booking.partials.billing_info')
                </div>
            </div>
        </div>

        <div class="col-xxl-6 col-lg-12 col-md-6">
            <div class="row gy-4">
                <div class="col-12">
                    @include('owner.booking.partials.payment_summary')
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($refund > 0)
                                <h5 class="card-title">@lang('Refund Amount')</h5>
                                <h5 class="text--danger text-center">@lang('Refundable Amount'): {{ showAmount($refund) }}</h5>
                            @else
                                <h5 class="card-title"> @lang('Receive Payment')</h5>
                                <h5 class="text-center text--success"> @lang('Receivable Amount'): {{ showAmount(abs($due)) }}</h5>
                            @endif

                            <form action="{{ route('owner.booking.payment', $booking->id) }}" method="post">
                                @csrf
                                <div class="form-group">
    <label>@lang('Enter Amount')</label>
    <div class="input-group">
        <input 
        class="form-control"
        min="0"
        max="{{ $refund > 0 ? $refund : $due }}"
        name="amount"
        required
        step="0.01"
        type="number">
        <span class="input-group-text">{{ __(gs()->cur_text) }}</span>
    </div>
</div>
                                @can('owner.booking.payment')
<button 
    @disabled(abs($due) == 0 && $refund == 0)
    class="btn {{ $refund > 0 ? 'btn--danger' : 'btn--primary' }} w-100 h-45"
    type="submit">

    {{ $refund > 0 ? __('Process Refund') : __('Receive Payment') }}

</button>
@endcan
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @can('owner.booking.extra.charge')
            <div class="modal fade" id="extraChargeModal" role="dialog" tabindex="-1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                                <i class="las la-times"></i>
                            </button>
                        </div>
                        <form action="" method="post">
                            @csrf
                            <input name="type" type="hidden">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>@lang('Amount')</label>
                                    <div class="input-group">
                                        <input class="form-control" min="0" name="amount" required step="any" type="number">
                                        <span class="input-group-text">{{ __(gs()->cur_text) }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Reason')</label>
                                    <textarea class="form-control" name="reason" required rows="4"></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    @endsection

    @push('breadcrumb-plugins')
        @can('owner.booking.extra.charge.add')
            <button class="btn btn--success extraChargeBtn" data-id="{{ $booking->id }}" data-type="add">
                <i class="las la-plus-circle"></i>@lang('Add Extra Charge')
            </button>
        @endcan

        @can('owner.booking.extra.charge.subtract')
            <button class="btn btn--danger extraChargeBtn" data-id="{{ $booking->id }}" data-type="subtract"><i class="las la-minus-circle"></i>@lang('Subtract Extra Charge')</button>
        @endcan
    @endpush

    @push('script')
        <script>
            (function($) {
                "use strict";
                $('.extraChargeBtn').on('click', function() {
                    let data = $(this).data();
                    let modal = $('#extraChargeModal');
                    modal.find('.modal-title').text($(this).text());
                    if (data.type == 'add') {
                        modal.find('form').attr('action', `{{ route('owner.booking.extra.charge.add', '') }}/${data.id}`);
                        modal.find('[name=type]').val('add');
                    } else {
                        modal.find('form').attr('action', `{{ route('owner.booking.extra.charge.subtract', '') }}/${data.id}`);
                        modal.find('[name=type]').val('subtract');
                    }
                    modal.modal('show');
                });
            })(jQuery);
        </script>
    @endpush

    @push('style')
        <style>
            .list .list-item {
                border: 1px solid #f1f1f1;
                border-bottom: 0;
                display: flex;
                justify-content: space-between;
                padding: 0.6rem;
            }

            .list .list-item span:first-child {
                font-weight: 500;
                border-radius: 7px 7px 0 0;
            }

            .list .list-item:first-child {
                border-radius: 7px 7px 0 0;
            }

            .list .list-item:last-child {
                border-bottom: 1px solid #f1f1f1;
                border-radius: 0 0 7px 7px;
            }
        </style>
    @endpush
