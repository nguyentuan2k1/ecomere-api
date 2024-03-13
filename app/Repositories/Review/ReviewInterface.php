<?php

namespace App\Repositories\Review;

interface ReviewInterface
{
    public function getProductRating($id);

    public function getReviews();
}
