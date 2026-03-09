@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Owner')</th>
                                    <th>@lang('Login at')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('Browser | OS')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loginLogs as $log)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __(@$log->owner->fullname) }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.owners.detail', $log->owner_id) }}">
                                                    {{ @$log->owner->email }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            {{ showDateTime($log->created_at) }} <br> {{ diffForHumans($log->created_at) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                <a
                                                    href="{{ route('admin.report.owner.login.ip.history', [$log->owner_ip]) }}">
                                                    {{ $log->owner_ip }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>{{ __($log->city) }} <br> {{ __($log->country) }}</td>
                                        <td>{{ __($log->browser) }} <br> {{ __($log->os) }}</td>
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
                @if ($loginLogs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($loginLogs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if (request()->routeIs('admin.report.owner.login.history'))
        <x-search-form placeholder="Enter Username" dateSearch="yes" />
    @endif
@endpush

@if (request()->routeIs('admin.report.owner.login.ip.history'))
    @push('breadcrumb-plugins')
        <a class="btn btn--primary" href="https://www.ip2location.com/{{ $ip }}"
            target="_blank">@lang('Lookup IP') {{ $ip }}</a>
    @endpush
@endif
