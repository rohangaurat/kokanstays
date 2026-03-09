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
                                    <th>@lang('Location')</th>
                                    <th>@lang('Vendor')</th>
                                    <th>@lang('Phone') | @lang('Email')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Is Featured')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($owners as $owner)
                                    <tr>
                                        <td><span class="fw-bold">{{ @$owner->hotelSetting->name }}</span></td>
                                        <td>
                                            <div>
                                                <span>
                                                    {{ __(@$owner->hotelSetting->location->name) }},
                                                    {{ __(@$owner->hotelSetting->city->name) }}
                                                </span>
                                                <br>
                                                <span>{{ __(@$owner->hotelSetting->country->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.owners.detail', $owner->id) }}">
                                                {{ $owner->fullname }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-bold">+{{ $owner->mobile }}</span> <br>
                                            <span class="fw-bold">{{ $owner->email }}</span>
                                        </td>
                                        <td>
                                            {{ showDateTime($owner->created_at) }} <br>
                                            {{ diffForHumans($owner->created_at) }}
                                        </td>
                                        <td>@php echo $owner->featureBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button aria-expanded="false" class="btn btn-sm btn-outline--info"
                                                    data-bs-toggle="dropdown" type="button">
                                                    <i class="las la-ellipsis-v"></i>@lang('More')
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.owners.detail', $owner->id) }}">
                                                        <i class="las la-desktop"></i> @lang('Details')
                                                    </a>
                                                    @if (!Route::is('admin.owners.banned'))
                                                        @if ($owner->is_featured)
                                                            <button class="dropdown-item confirmationBtn"
                                                                data-question="@lang('Are you sure, you want to unfeature this vendor?')"
                                                                data-action="{{ route('admin.owners.feature.status.update', $owner->id) }}"><i
                                                                    class="las la-times"></i> @lang('Unfeature')</button>
                                                        @else
                                                            <button class="dropdown-item confirmationBtn"
                                                                data-question="@lang('Are you sure, you want to featured this vendor?')"
                                                                data-action="{{ route('admin.owners.feature.status.update', $owner->id) }}"><i
                                                                    class="las la-check"></i> @lang('Feature')</button>
                                                        @endif
                                                    @endif
                                                    <a href="{{ route('admin.owners.hotel.setting', $owner->id) }}"
                                                        class="dropdown-item">
                                                        <i class="las la-cog"></i> @lang('Hotel Configuration')
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.owners.login', $owner->id) }}"
                                                        target="_blank">
                                                        <i class="las la-sign-in-alt"></i> @lang('Login as Vendor')
                                                    </a>
                                                </div>
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

@push('style')
    <style>
        .dropdown-item {
            font-size: 14px !important;
        }
    </style>
@endpush
