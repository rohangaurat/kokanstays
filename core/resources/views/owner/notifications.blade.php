@extends('owner.layouts.app')
@section('panel')
    <div class="notify__area">
        @forelse($notifications as $notification)
            <div class="notify-item-wrapper">
                @can('owner.notification.read')
                    <a class="notify__item @if ($notification->is_read == Status::NO) unread--notification @endif"
                        href="{{ route('owner.notification.read', $notification->id) }}">
                        <div class="notify__content d-flex justify-content-between">
                            <div>
                                <h6 class="title">{{ __($notification->title) }}</h6>
                                <span class="date">
                                    <i class="las la-clock"></i> {{ diffForHumans($notification->created_at) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endcan
                @can('owner.notification.delete.single')
                    <button type="button" class="btn btn-sm btn-outline--danger notify-delete-btn confirmationBtn"
                        data-question="@lang('Are you sure to delete this notification?')"
                        data-action="{{ route('owner.notifications.delete.single', $notification->id) }}">
                        <i class="las la-trash me-0"></i>
                    </button>
                @endcan
            </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <div class="empty-notification-list text-center">
                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                        <h5 class="text-muted">@lang('No notification found.')</h5>
                    </div>
                </div>
            </div>
        @endforelse
        <div class="mt-3">
            {{ paginateLinks($notifications) }}
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    @can('owner.notifications.readAll')
        @if ($hasUnread)
            <a class="btn btn-sm btn-outline--primary" href="{{ route('owner.notifications.readAll') }}">
                @lang('Mark All as Read')
            </a>
        @endif
    @endcan
    @can('owner.notifications.delete.all')
        @if (!blank($notifications))
            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                data-action="{{ route('owner.notifications.delete.all') }}" data-question="@lang('Are you sure to delete all notifications?')">
                <i class="las la-trash"></i>@lang('Delete all Notification')
            </button>
        @endif
    @endcan
@endpush

@push('style')
    <style>
        .notify-item-wrapper {
            position: relative;
        }

        .notify-item-wrapper .notify-delete-btn {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .empty-notification-list img {
            width: 120px;
            margin-bottom: 15px;
        }
    </style>
@endpush
