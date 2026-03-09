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
                                    <th>@lang('Booking No.')</th>
                                    <th>@lang('Details')</th>
                                    <th>@lang('Action By')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookingLog as $log)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ can('owner.booking.all') ? route('owner.booking.all', ['search' => @$log->booking->booking_number]) : 'javascript:void(0)' }}">
                                                {{ @$log->booking->booking_number }}
                                            </a>
                                        </td>
                                        <td>{{ $log->details ? __($log->details) : __(keyToTitle($log->remark)) }}</td>
                                        <td>{{ __(@$log->actionBy->fullname) }}</td>
                                        <td>
                                            {{ showDateTime($log->created_at) }} <br>
                                            {{ diffForHumans($log->created_at) }}
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
                @if ($bookingLog->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($bookingLog) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Booking No." />
    <form action="" class="form-search" method="GET">
        <select class="form-control select2" data-minimum-results-for-search="-1" name="remark">
            <option value="">@lang('All')</option>
            @foreach ($remarks as $remark)
                <option value="{{ $remark->remark }}" @selected(request('remark') == $remark->remark)>
                    {{ __(keyToTitle($remark->remark)) }}
                </option>
            @endforeach
        </select>
    </form>
@endpush

@push('script')
    <script>
        "use strict";

        $('[name=remark]').on('change', function() {
            $('.form-search').submit();
        })

        @if (request()->remark)
            let remark = @json(request()->remark);
            $(`[name=remark] option[value="${remark}"]`).prop('selected', true);
        @endif
    </script>
@endpush

@push('style')
    <style>
        .select2-container--default .select2-selection--single {
            width: 200px !important;
        }
        .select2-container {
            z-index: 99 !important;
        }
    </style>
@endpush
