<?php

namespace App\Repositories\Review;

use App\Models\Review;

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
        $product_id = $params[0];
        $page = $params[1];
        $limit = $params[2];

        // Fetch reviews with user relation
        $reviews = Review::query()
            ->where("product_id", $product_id)
            ->orderBy("created_at", "DESC")
            ->with("user", "reviewHelpful")->get();


        foreach ($reviews as $review) {
            $data = [
                "id" => $review->id,
                "user_id" => $review->user_id,
                "rating" => $review->rating,
                "content" => $review->content,
                "product_id" => $review->product_id,
                "created_at" => $review->created_at,
                "updated_at" => $review->updated_at,
                "author_name" => $review->user->full_name,
                "author_avatar" => getFileInStorage($review->user->avatar),
                "is_helpful" => empty($review->reviewHelpful),
            ];

            $reviews_data[] = $data;
        }

        return $reviews_data ?? [];
    }
}


