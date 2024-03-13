<?php

namespace App\Repositories\Review;

use App\Models\Review;
use App\Models\ReviewHelpful;

class ReviewRepository implements ReviewInterface
{
    /**
     * Get product Rating
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getProductRating($id)
    {
        return Review::query()->where("product_id", $id)
            ->select(["rating"])
            ->get();
    }

    /**
     * Get review
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getProductReviews($params = [])
    {
        $user                     = auth()->guard('api')->user();
        $reviews                  = Review::query()
                                    ->where("product_id", $product_id)
                                    ->with(['user'])
                                    ->orderBy("created_at", "DESC");

        $reviews                  = $reviews->paginate($params['limit'], ['*'], 'page', $params['page']);

        $reviews->getCollection()->transform(function ($review) use ($user) {
            $review->user->avatar = getFileInStorage($review->user->avatar);
            $getReviewHelpful     = ReviewHelpful::query()
                                    ->where("review_id", $review->id)
                                    ->where("user_id",$user->id)
                                    ->first();
            $review->is_helpful   = !empty($getReviewHelpful);
            return $review;
        });

        return $reviews;
    }
}


