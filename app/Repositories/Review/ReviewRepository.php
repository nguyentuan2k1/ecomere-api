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

        $reviews = Review::query()
            ->where("product_id", $product_id)
            ->with(['reviewHelpFul'])
            ->orderBy("created_at", "DESC");

        if (!empty($params['limit'])) return $reviews->paginate($params['limit']);

        return $reviews->get();
    }
}


