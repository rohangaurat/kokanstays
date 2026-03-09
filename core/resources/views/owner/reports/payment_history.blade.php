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
                                    <th>@lang('User')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Payment Via')</th>
                                    <th>@lang('Issued By')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($paymentLog as $log)
                                    <tr>
                                        <td>{{ @$log->booking->booking_number }}</td>
                                        <td>
                                            @if (@$log->booking->user_id)
                                                {{ __($log->booking->user->fullname) }} <br>
                                                <span class="small">{{ $log->booking->user->email }}</span>
                                            @else
                                                {{ __(@$log->booking->guest->name) }}
                                                <br>
                                                <span class="small">{{ @$log->booking->guest->email }}</span>
                                            @endif
                                        </td>
                                        <td>{{ showAmount($log->amount) }}</td>
                                        <td><span>{{ __($log->payment_system) }}</span></td>
                                        <td>
                                            @if ($log->action_by)
                                                {{ __($log->actionBy->fullname) }}
                                            @else
                                                <span class="text--cyan">@lang('Direct Payment')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($log->created_at) }}
                                            <br>
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
                @if ($paymentLog->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($paymentLog) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="User/Booking No." />
@endpush
