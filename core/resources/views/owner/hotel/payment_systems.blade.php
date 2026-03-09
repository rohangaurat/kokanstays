@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Method Name')</th>
                                    <th>@lang('Status')</th>
                                    @can(['owner.hotel.setting.payment.system.update', 'owner.hotel.setting.payment.system.status.update'])
                                        <th>@lang('Action')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentSystems as $paymentSystem)
                                    <tr>
                                        <td>{{ __($paymentSystem->name) }}</td>
                                        <td>@php echo $paymentSystem->statusBadge;@endphp</td>
                                        @can(['owner.hotel.setting.payment.system.update', 'owner.hotel.setting.payment.system.status.update'])
                                            <td>
                                                <div class="button--group">
                                                    @can('owner.hotel.setting.payment.system.update')
                                                        <button class="btn btn-sm btn-outline--primary editBtn" data-admin_id="{{ $paymentSystem->admin_id }}" data-id="{{ $paymentSystem->id }}" data-name="{{ $paymentSystem->name }}" type="button"><i class="la la-pencil"></i>@lang('Edit')</button>
                                                    @endcan

                                                    @can('owner.hotel.setting.payment.system.status.update')
                                                        @if ($paymentSystem->status)
                                                            <button class="btn btn-sm confirmationBtn btn-outline--danger" data-action="{{ route('owner.hotel.setting.payment.system.status.update', $paymentSystem->id) }}" data-question="@lang('Are you sure to disable this payment method?')" type="button">
                                                                <i class="las la-eye-slash"></i>@lang('Disable')
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm confirmationBtn btn-outline--success" data-action="{{ route('owner.hotel.setting.payment.system.status.update', $paymentSystem->id) }}" data-question="@lang('Are you sure to enable this payment method?')" type="button">
                                                                <i class="las la-eye"></i>@lang('Enable')
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcan
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
            </div>
        </div>
    </div>
    @can(['owner.hotel.setting.payment.system.add', 'owner.hotel.setting.payment.system.update'])
        <div class="modal fade" id="paymentMethodModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>@lang('Name')</label>
                                <input class="form-control" name="name" required type="text">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('owner.hotel.setting.payment.system.status.update')
        <x-confirmation-modal />
    @endcan
@endsection

@can('owner.hotel.setting.payment.system.add')
    @push('breadcrumb-plugins')
        <button class="btn btn-sm btn-outline--primary addBtn" type="button">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endpush
@endcan

@push('script')
    <script>
        (function($) {
            "use strict";
            let modal = $('#paymentMethodModal');
            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Payment System')`);
                let url = `{{ route('owner.hotel.setting.payment.system.add') }}`;
                modal.find('form').attr('action', url);
                modal.find('form').trigger("reset");
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                let data = $(this).data();
                let url = `{{ route('owner.hotel.setting.payment.system.update', '') }}/${data.id}`;

                modal.find('.modal-title').text(`@lang('Update Payment System')`);
                modal.find('form').attr('action', url);
                modal.find('[name=name]').val(data.name);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
