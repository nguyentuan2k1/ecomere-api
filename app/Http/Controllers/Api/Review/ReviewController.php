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

    public function getProductReviews(Request $request)
    {
        try {
            $productId = $request->get("product_id");
            $params    = [];

            if (intval($request->get("limit"))
                && $request->get("limit") > 0
            ) {
                $params['limit'] = $request->get("limit");
            } else {
                $params['limit'] = 5;
            }

            if (intval($request->get("page"))
                && $request->get("page") > 0
            ) {
                $params['page'] = $request->get("page");
            } else {
                $params['page'] = 1;
            }

            $productReviews = $this->reviewService->getProductReviews($productId, $params);

            foreach ($productReviews as $review) {
                $review->getIsHelpfulAttribute();
                $review->user->getAvatarAttribute();
            }

            return $this->sendPaginationResponse($productReviews);
        } catch (\Exception $exception) {
            $erros = $exception->getMessage() . " - Line: " . $exception->getLine() . " - File: " . $exception->getFile();
            Log::error($erros);

            return $this->sendError("Have an error. Please try again", 500);
        }
    }
}
