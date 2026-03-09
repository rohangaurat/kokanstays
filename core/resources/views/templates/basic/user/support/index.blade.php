@extends('Template::layouts.master')
@section('content')
    <div class="custom--card card mb-3">
        <div class="card-header">
            <div class="card-header__wrapper flex-wrap">
                <div>
                    <h5 class="profile-wrapper__title">{{ __($pageTitle) }}</h5>
                    <p class="profile-wrapper__text mb-0">
                        @lang('Track and manage your support requests in one place.')
                    </p>
                </div>
                <div>
                    <a href="{{ route('ticket.open') }}" class="btn btn--base">
                        <i class="las la-plus"></i> @lang('Open New Ticket')
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="custom--card card">
        <div class="card-body p-0">
            @if (!blank($supports))
                <div class="accordion table--acordion custom--accordion" id="transactionAccordion">
                    @foreach ($supports as $support)
                        <div class="accordion-item transaction-item">
                            <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#c-{{ $loop->iteration }}">
                                    <div class="col-lg-6 col-sm-5 col-8 order-1 icon-wrapper">
                                        <div class="left">
                                            <div class="content p-0">
                                                <h6 class="trans-title">
                                                    [@lang('Ticket')#{{ $support->ticket }}]
                                                </h6>
                                                <span class="text-muted fs-14 mt-2">
                                                    {{ strLimit(__($support->subject), 30) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                                        <p class="text-muted fs-14 text-start">
                                            {{ showDateTime($support->created_at, 'M d Y @g:i:a') }}
                                            <br>
                                            {{ diffForHumans($support->last_reply) }}
                                        </p>
                                    </div>
                                    <div class="col-lg-2 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                        @php echo $support->statusBadge; @endphp
                                    </div>
                                </button>
                            </h2>
                            <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                                data-bs-parent="#transactionAccordion">
                                <div class="accordion-body">
                                    <ul class="caption-list">
                                        <li>
                                            <span class="caption">@lang('Priority')</span>
                                            <span class="value">@php echo $support->priorityBadge; @endphp</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Action')</span>
                                            <span class="value">
                                                <a href="{{ route('ticket.view', $support->ticket) }}"
                                                    class="btn btn-outline--base btn--sm">
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </a>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @include('Template::partials.empty_list', ['message' => 'No ticket found.'])
            @endif
        </div>
    </div>

    @if ($supports->hasPages())
        <div class="mt-4">{{ paginateLinks($supports) }}</div>
    @endif
@endsection

@push('style')
    <style>
        @media (max-width: 575px) {
            .table--acordion .left .content {
                width: 100%;
            }
        }
    </style>
@endpush
