<?php

namespace App\Http\Controllers\Owner;

use App\Models\Review;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index()
    {
        $pageTitle = 'Reviews';
        $owner = authOwner();
        $reviews = Review::reviews()->whereHasAuthOwner($owner->id)->with(['replies', 'user'])->orderByDesc('updated_at')->paginate(getPaginate());

        return view('owner.reviews.index', compact('pageTitle', 'reviews'));
    }

    public function reviewDetail($id)
    {
        $pageTitle = 'Review Detail';
        $owner = authOwner();
        $review = Review::reviews()->whereHasAuthOwner($owner->id)->with(['replies', 'user'])->findOrFail($id);

        return view('owner.reviews.detail', compact('pageTitle', 'review'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required',
        ]);

        $owner = authOwner();
        $review = Review::reviews()->whereHasAuthOwner($owner->id)->findOrFail($id);

        $reply = new Review();
        $reply->type = Status::REVIEW_TYPE_OWNER;
        $reply->parent_id = $review->id;
        $reply->owner_id = $review->owner_id;
        $reply->user_id = $review->user_id;
        $reply->comment = $request->comment;
        $reply->save();

        $notify[] = ['success', 'Reply has been added successfully'];
        return back()->withNotify($notify);
    }
}
