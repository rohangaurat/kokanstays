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
                                    <th>@lang('Created At')</th>
                                    @can('owner.roles.edit')
                                        <th>@lang('Action')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ showDateTime($role->created_at) }}</td>
                                        @can('owner.roles.edit')
                                            <td>
                                                <div class="button--group">
                                                    <a class="btn btn-sm btn-outline--primary"
                                                        href="{{ route('owner.roles.edit', $role->id) }}">
                                                        <i class="las la-pencil-alt"></i>@lang('Edit')
                                                    </a>
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
@endsection

@can('owner.roles.add')
    @push('breadcrumb-plugins')
        <a class="btn btn-sm btn-outline--primary" href="{{ route('owner.roles.add') }}">
            <i class="las la-plus"></i>@lang('Add New')
        </a>
    @endpush
@endcan
