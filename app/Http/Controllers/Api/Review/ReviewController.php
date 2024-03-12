<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\BaseController;
use App\Service\Review\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends BaseController
{
    public ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function getProductRating(Request $request)
    {
        try {
            $rating         = $this->reviewService->getProductRating($request->get("product_id"));
            $totalRating    = collect($rating)->count();
            $data           = [];
            $totalRatingAvg = 0;

            for ($i = 1; $i <= 5; $i++) {
                $data["total_rating_star_{$i}"]         = collect($rating)->where("rating", $i)->count();
                $data["total_rating_star_{$i}_percent"] = round(($data["total_rating_star_{$i}"] / $totalRating * 100), 2);

                $totalRatingAvg                         += $data["total_rating_star_{$i}"] * $i;
            }

            $totalRatingAvg   = round(($totalRatingAvg / $totalRating), 1);

            $data             = array_merge([
                "total_rating"     => $totalRating,
                "total_rating_avg" => $totalRatingAvg
            ], $data);

            return $this->sendResponse($data);
        } catch (\Exception $exception) {
            Log::error("{$exception->getMessage()}- Line: {$exception->getLine()} - File: {$exception->getFile()}");

            return $this->sendError("Have an error. Please try again", 500);
        }
    }
}
