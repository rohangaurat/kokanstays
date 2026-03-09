@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Hotel')</th>
                                    <th>@lang('Vendor')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Requested At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($owners as $owner)
                                    <tr>
                                        <td>{{ __(@$owner->hotelSetting->name) }}</td>
                                        <td>{{ $owner->fullname }}</td>
                                        <td>{{ __(@$owner->hotelSetting->location->name) }}</td>
                                        <td>{{ __(@$owner->hotelSetting->city->name) }}</td>
                                        <td>{{ __(@$owner->hotelSetting->country->name) }}</td>
                                        <td>
                                            {{ showDateTime($owner->created_at, 'd M, Y h:iA') }}
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.vendor.request.detail', $owner->id) }}">
                                                    <i class="las la-desktop"></i>@lang('Details')
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
                @if ($owners->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($owners) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />
@endpush
