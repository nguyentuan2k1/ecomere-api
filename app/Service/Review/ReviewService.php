<?php

namespace App\Service\Review;

use App\Repositories\Review\ReviewInterface;

class ReviewService
{
    public ReviewInterface $reviewRepository;

    public function __construct(ReviewInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * Get total rating
     * @param int $id
     * @return mixed
     */
    public function getProductRating($id)
    {
        return $this->reviewRepository->getProductRating($id);
    }

    /**
     * Get product reviews
     * @param array $param
     * @return mixed
     */
    public function getProductReviews($product_id, $params)
    {
        return $this->reviewRepository->getProductReviews($product_id, $params);
    }

    /**
     * Get product reviews
     * @param int $product_id, $user_id
     * @return mixed
     */
    public function helpfulReview($product_id, $user_id)
    {
        return $this->reviewRepository->helpfulReview($product_id, $user_id);
    }
}
