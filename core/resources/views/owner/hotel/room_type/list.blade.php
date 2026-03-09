@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Fare')</th>
                                    <th>@lang('Rooms')</th>
                                    <th>@lang('Adult')</th>
                                    <th>@lang('Child')</th>
                                    <th>@lang('Feature Status')</th>
                                    <th>@lang('Status')</th>
                                    @can(['owner.hotel.room.type.edit', 'owner.hotel.room.type.status'])
                                        <th>@lang('Action')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($typeList as $type)
                                    <tr>
                                        <td><span class="fw-bold">{{ __($type->name) }}</span></td>
                                        <td><span class="fw-bold">{{ showAmount($type->fare) }}</span></td>
                                        <td>{{ $type->rooms_count ?? 0 }}</td>
                                        <td>{{ $type->total_adult }}</td>
                                        <td>{{ $type->total_child }}</td>
                                        <td>@php echo $type->featureBadge;  @endphp</td>
                                        <td>@php echo $type->statusBadge;  @endphp</td>
                                        @can(['owner.hotel.room.type.edit', 'owner.hotel.room.type.status'])
                                            <td>
                                                <div class="button--group">
                                                    @can('owner.hotel.room.type.edit')
                                                        <a class="btn btn-sm btn-outline--primary"
                                                            href="{{ route('owner.hotel.room.type.edit', $type->id) }}"> <i
                                                                class="la la-pencil"></i>@lang('Edit')
                                                        </a>
                                                    @endcan
                                                    @can('owner.hotel.room.type.status')
                                                        @if ($type->status == 0)
                                                            <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                                data-action="{{ route('owner.hotel.room.type.status', $type->id) }}"
                                                                data-question="@lang('Are you sure to enable this room type?')">
                                                                <i class="la la-eye"></i>@lang('Enable')
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                                data-action="{{ route('owner.hotel.room.type.status', $type->id) }}"
                                                                data-question="@lang('Are you sure to disable this room type?')">
                                                                <i class="la la-eye-slash"></i>@lang('Disable')
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
                @if ($typeList->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($typeList) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @can('owner.hotel.room.type.status')
        <x-confirmation-modal />
    @endcan
@endsection

@can('owner.hotel.room.type.create')
    @push('breadcrumb-plugins')
        <a class="btn btn-sm btn-outline--primary" href="{{ route('owner.hotel.room.type.create') }}">
            <i class="las la-plus"></i>@lang('Add New')
        </a>
    @endpush
@endcan
