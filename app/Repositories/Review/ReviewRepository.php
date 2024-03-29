<?php

namespace App\Repositories\Review;

use App\Models\Review;
use App\Models\ReviewHelpful;
use App\Models\User;

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
     */
    public function getProductReviews($product_id, $params = [])
    {
        $reviews = Review::query()
            ->where("reviews.product_id", $product_id)
            ->with(['user'])
            ->withCount(['reviewHelpful as is_helpful'])
            ->orderBy("created_at", "DESC");

        if (empty($params['limit'])
            || !intval($params['limit'])
            || $params['limit'] <= 0
        ) return $reviews->get();

        return $reviews->paginate($params['limit'], ['*'], 'page', $params['page']);
    }

    /**
     * helpfulReview
     * @param int $review_id , $user_id
     * @return mixed
     */
    public function helpfulReview($review_id, $user_id)
    {
        $helpfulReview = ReviewHelpful::query()
            ->where("review_id", $review_id)
            ->where("user_id", $user_id)
            ->first();

        if (!empty($helpfulReview)) {
            $helpfulReview->delete();

            return $helpfulReview;
        }

        ReviewHelpful::create([
            "review_id" => $review_id,
            "user_id" => $user_id
        ])->save();

        return null;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function sendReview($data)
    {
        $review = new Review();

        foreach ($data as $field => $value) {
            $review->$field = $value;
        }

        $review->save();

        $review->user;

        $review->is_helpful = empty($data['is_helpful']) ? false : $data['is_helpful'];

        return $review;
    }
}


