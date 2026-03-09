@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Last Reply')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td>
                                            <a class="fw-bold" href="{{ route('owner.ticket.view', $item->ticket) }}">
                                                [@lang('Ticket')#{{ $item->ticket }}] {{ strLimit($item->subject, 30) }}
                                            </a>
                                        </td>
                                        <td>@php echo $item->statusBadge; @endphp</td>
                                        <td>@php echo $item->priorityBadge; @endphp</td>
                                        <td>{{ diffForHumans($item->last_reply) }}</td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--primary ms-1" href="{{ route('owner.ticket.view', $item->ticket) }}">
                                                    <i class="las la-desktop"></i> @lang('View')
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
                @if ($items->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($items) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@can('owner.ticket.open')
    @push('breadcrumb-plugins')
        <a class="btn btn-sm btn--primary" href="{{ route('owner.ticket.open') }}"><i class="las la-plus"></i>@lang('Add New')</a>
    @endpush
@endcan
