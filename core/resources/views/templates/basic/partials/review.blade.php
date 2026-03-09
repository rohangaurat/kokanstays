@if (!blank($reviews))
    <div class="hotel-details__item widget_component-wrapper" id="scrollHeadingFive">
        <h5 class="title skeleton">@lang('Reviews')</h5>
        <div class="testimonial-wrapper">
            @foreach ($reviews as $review)
                <div class="testimonials-card">
                    <div class="testimonial-item">
                        <div class="testimonial-item__info">
                            <div class="testimonial-item__thumb skeleton">
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $review->user->image ?? null, isAvatar: true) }}"
                                    class="fit-image" alt="profile image">
                            </div>
                            <div class="testimonial-item__details">
                                <h6 class="testimonial-item__name skeleton">{{ __($review->user->fullname ?? '') }}</h6>
                                <div class="testimonial-item__rating skeleton">
                                    <x-rating-star :rating="$review->rating" />
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-item__content">
                            <div class="testimonial-item__top">
                                <h6 class="testimonial-item__title skeleton">{{ __($review->title ?? '') }}</h6>
                                <p class="text skeleton">{{ showDateTime($review->created_at ?? now(), 'd F, Y') }}</p>
                            </div>
                            <p class="testimonial-item__desc skeleton">{{ __($review->comment ?? '') }}</p>
                            @if (!blank($review->replies))
                                <div class="comment-reply">
                                    <div class="comment-box__content">
                                        <div class="comment-bow-wrapper">
                                            @foreach ($review->replies as $reply)
                                                <div class="comment-box-item comment-item">
                                                    <div class="comment-box-item__thumb skeleton">
                                                        @php
                                                            if ($reply->type == Status::REVIEW_TYPE_USER) {
                                                                $replyUser = $reply->user;
                                                                $filePath = 'userProfile';
                                                                $name = $replyUser->fullname;
                                                            } else {
                                                                $replyUser = $reply->owner->hotelSetting;
                                                                $filePath = 'hotelImage';
                                                                $name = $replyUser->name;
                                                            }
                                                        @endphp
                                                        <img src="{{ getImage(getFilePath($filePath) . '/' . $replyUser->image ?? null, isAvatar: true) }}"
                                                            alt="profile image">
                                                    </div>
                                                    <div class="comment-box-item__content">
                                                        <p class="comment-box-item__name">
                                                            <span class="name skeleton">{{ __($name ?? '') }}</span>
                                                            <span
                                                                class="time skeleton">{{ showDateTime($reply->created_at ?? now(), 'd F, Y') }}</span>
                                                        </p>
                                                        <p class="comment-box-item__text skeleton">
                                                            {{ __($reply->comment ?? '') }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (auth()->check() && $review->user_id == auth()->id())
                                <div class="comment-reply">
                                    <button class="reply-btn skeleton">
                                        @lang('Reply')
                                        <span class="reply-btn__icon">
                                            <i class="lab la-telegram-plane"></i>
                                        </span>
                                    </button>
                                    <div class="comment-form-wrapper">
                                        <form class="comment-form"
                                            action="{{ route('user.review.reply', $review->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group position-relative">
                                                <textarea class="form--control" name="reply">{{ old('reply') }}</textarea>
                                                <button class="comment-btn skeleton less-border" type="submit">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
