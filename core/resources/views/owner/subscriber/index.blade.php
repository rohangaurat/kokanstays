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
                                    <th>@lang('Email')</th>
                                    <th>@lang('Subscribe At')</th>
                                    @can('owner.subscriber.remove')
                                        <th>@lang('Action')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscribers as $subscriber)
                                    <tr>
                                        <td>{{ $subscriber->email }}</td>
                                        <td>{{ showDateTime($subscriber->created_at) }}</td>
                                        @can('owner.subscriber.remove')
                                            <td>
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('owner.subscriber.remove', $subscriber->id) }}" data-question="@lang('Are you sure to remove this subscriber?')" type="button">
                                                    <i class="las la-trash"></i> @lang('Remove')
                                                </button>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($subscribers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($subscribers) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>

    </div>

    @can('owner.subscriber.remove')
        <x-confirmation-modal />
    @endcan
@endsection

@can('owner.subscriber.send.email')
    @push('breadcrumb-plugins')
        <a class="btn btn-sm btn-outline--primary" href="{{ route('owner.subscriber.send.email') }}"><i class="las la-paper-plane"></i>@lang('Send Email')</a>
    @endpush
@endcan
