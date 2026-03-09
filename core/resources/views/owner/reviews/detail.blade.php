@extends('owner.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom--card">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="mb-2">{{ __($review->title) }}</h5>
                        <x-rating-star :rating="$review->rating" />
                    </div>
                </div>
                <div class="card-body">
                    <div class="review-card user_card">
                        <h5>{{ $review->user->fullname }}</h5>
                        <p class="text-muted mb-3">
                            @lang('Posted on') {{ showDateTime($review->created_at, 'l, dS F Y @ H:i') }}
                        </p>
                        <p class="comment">{{ $review->comment }}</p>
                    </div>
                    @if (!blank($review->replies))
                        @foreach ($review->replies as $reply)
                            @if ($reply->type == Status::REVIEW_TYPE_USER)
                                <div class="review-card user_card">
                                    <h5>{{ $reply->user->fullname }}</h5>
                                    <p class="text-muted mb-3">
                                        @lang('Posted on') {{ showDateTime($reply->created_at, 'l, dS F Y @ H:i') }}
                                    </p>
                                    <p class="comment">{{ $reply->comment }}</p>
                                </div>
                            @elseif($reply->type == Status::REVIEW_TYPE_OWNER)
                                <div class="review-card vendor_card">
                                    <h5>{{ $reply->owner->hotelSetting->name }}</h5>
                                    <p class="text-muted mb-3">
                                        @lang('Posted on') {{ showDateTime($reply->created_at, 'l, dS F Y @ H:i') }}
                                    </p>
                                    <p class="comment">{{ $reply->comment }}</p>
                                </div>
                            @endif
                        @endforeach
                    @endif
                    @can('owner.review.reply')
                        <form action="{{ route('owner.review.reply', $review->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <textarea class="form-control" name="comment" rows="5" required placeholder="@lang('Enter reply here')">{{ old('comment') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @can('owner.review.index')
        <x-back route="{{ route('owner.review.index') }}" />
    @endcan
@endpush

@push('style')
    <style>
        .review-card {
            display: flex;
            flex-direction: column;
            align-items: start;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .review-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .review-card:last-child {
            margin-bottom: 0;
        }

        .user_card {
            background-color: #1ea1f211;
        }

        .vendor_card {
            background-color: #4534ff0f;
        }

        .comment {
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }

        .rating-list {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .rating-list__item {
            padding: 0 2px;
            color: #ff9f43;
            font-size: 14px;
            line-height: 1;
        }
    </style>
@endpush
