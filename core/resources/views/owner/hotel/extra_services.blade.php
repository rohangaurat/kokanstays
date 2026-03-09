@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Fee')</th>
                                    <th>@lang('Status')</th>
                                    @can(['owner.hotel.extra_services.save', 'owner.hotel.extra_services.status'])
                                        <th>@lang('Action')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($extraServices as $extraService)
                                    <tr>
                                        <td><span class="fw-bold">{{ __($extraService->name) }}</span></td>
                                        <td><span class="fw-bold">{{ showAmount($extraService->cost) }}</span></td>
                                        <td>@php echo $extraService->statusBadge; @endphp</td>
                                        @can(['owner.hotel.extra_services.save', 'owner.hotel.extra_services.status'])
                                            <td>
                                                <div class="button--group">
                                                    @can('owner.hotel.extra_services.save')
                                                        <button class="btn btn-sm btn-outline--primary cuModalBtn"
                                                            data-has_status="1" data-modal_title="@lang('Update Premium Service')"
                                                            data-resource="{{ $extraService }}" type="button">
                                                            <i class="la la-pencil"></i>@lang('Edit')
                                                        </button>
                                                    @endcan
                                                    @can('owner.hotel.extra_services.status')
                                                        @if ($extraService->status == Status::DISABLE)
                                                            <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                                data-action="{{ route('owner.hotel.extra_services.status', $extraService->id) }}"
                                                                data-question="@lang('Are you sure to enable this extra service?')" type="button">
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                                data-action="{{ route('owner.hotel.extra_services.status', $extraService->id) }}"
                                                                data-question="@lang('Are you sure to disable this extra service?')" type="button">
                                                                <i class="la la-eye-slash"></i> @lang('Disable')
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ $emptyMessage }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($extraServices->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($extraServices) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @can('owner.hotel.extra_services.save')
        <div class="modal fade" id="cuModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('owner.hotel.extra_services.save') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label> @lang('Service Name')</label>
                                <input class="form-control" name="name" required type="text" value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label> @lang('Fee')</label>
                                <div class="input-group">
                                    <input class="form-control" name="cost" required step="0.01" type="number"
                                        value="{{ old('cost') }}">
                                    <span class="input-group-text"> {{ __(gs()->cur_text) }}</span>
                                </div>
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

    @can('owner.hotel.extra_services.status')
        <x-confirmation-modal />
    @endcan
@endsection

@can('owner.hotel.extra_services.save')
    @push('breadcrumb-plugins')
        <button class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add Premium Service')" type="button">
            <i class="las la-plus"></i>@lang('Add New ')
        </button>
    @endpush
@endcan
