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
        $reviews = Review::query()
            ->where("product_id", $product_id)
            ->with(['reviewHelpful', 'user'])
            ->orderBy("created_at", "DESC");

        $reviews = $reviews->paginate($params['limit']);

        $reviews->getCollection()->transform(function ($review) {
            $review->user->avatar = getFileInStorage($review->user->avatar);
            $review->is_helpful = (bool)$review->reviewHelpful;
            return $review;
        });

        return $reviews;
    }
}


