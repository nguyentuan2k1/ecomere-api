<?php

namespace App\Repositories\Review;

interface ReviewInterface
{
    public function getProductRating($id);

    public function getProductReviews($product_id, $params = []);

    public function helpfulReview($review_id, $user_id);

    public function sendReview($data);
}
