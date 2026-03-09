<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Models\Owner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\OwnerNotification;
use App\Models\Review;

class ReviewController extends Controller
{
    public function reviewSubmit(Request $request, $id)
    {
        $request->validate([
            'booking_id' => 'required|integer|exists:bookings,id',
            'comment' => 'required|string',
            'rating' => 'required|gt:0|min:1|max:5',
            'title' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $owner = Owner::find($id);
        if (!$owner) {
            $notify[] = 'Owner not found!';
            return responseError('owner_not_found', $notify);
        }
        $booking = Booking::where('user_id', $user->id)->where('owner_id', $id)->find($request->booking_id);

        if (!$booking) {
            $notify[] = 'Booking not found!';
            return responseError('booking_not_found', $notify);
        }

        if(Review::reviews()->where('booking_id', $request->booking_id)->first()){
            $notify[] = 'You have already submitted a review for this booking';
            return responseError('already_reviewed', $notify);
        }

        $review = new Review();
        $review->type = Status::REVIEW_TYPE_USER;
        $review->user_id = $user->id;
        $review->owner_id = $owner->id;
        $review->booking_id = $booking->id;
        $review->parent_id = 0;
        $review->title = $request->title;
        $review->comment = $request->comment;
        $review->rating = $request->rating;
        $review->save();

        $query = Review::reviews()->where('owner_id', $owner->id);
        $ownerTotalReviewCount = (clone $query)->count();
        $ownerTotalRatingSum = (clone $query)->sum('rating');

        $hotelSetting = $owner->hotelSetting;
        $hotelSetting->avg_rating = $ownerTotalRatingSum ? ($ownerTotalRatingSum / $ownerTotalReviewCount) : 0;
        $hotelSetting->save();

        $ownerNotification = new OwnerNotification();
        $ownerNotification->owner_id = $owner->id;
        $ownerNotification->user_id = $user->id;
        $ownerNotification->title = 'You have a new review';
        $ownerNotification->click_url = route('owner.review.details', $review->id);
        $ownerNotification->save();

        $notify[] = 'Review submitted successfully';
        return responseSuccess('review', $notify);
    }

    public function reviewReply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);

        $user = auth()->user();
        $review = Review::where('user_id', $user->id)->findOrFail($id);

        $reply = new Review();
        $reply->type = Status::REVIEW_TYPE_USER;
        $reply->parent_id = $review->id;
        $reply->user_id = $user->id;
        $reply->owner_id = $review->owner_id;
        $reply->comment = $request->reply;
        $reply->save();

        $ownerNotification = new OwnerNotification();
        $ownerNotification->owner_id = $review->owner_id;
        $ownerNotification->user_id = $user->id;
        $ownerNotification->title = 'New reply on a review';
        $ownerNotification->click_url = route('owner.review.details', $review->id);
        $ownerNotification->save();

        $notify[] = 'Reply submitted successfully';
        return responseSuccess('reply', $notify);
    }
}
