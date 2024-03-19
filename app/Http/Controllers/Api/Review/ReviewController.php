<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\BaseController;
use App\Service\Review\ReviewService;
use App\Service\UploadFile\UploadFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReviewController extends BaseController
{
    public ReviewService $reviewService;

    private UploadFileService $uploadFileService;

    public function __construct(
        ReviewService     $reviewService,
        UploadFileService $uploadFileService
    )
    {
        $this->reviewService = $reviewService;
        $this->uploadFileService = $uploadFileService;
    }

    public function getProductRating(Request $request)
    {
        try {
            $rating = $this->reviewService->getProductRating($request->get("product_id"));
            $totalRating = collect($rating)->count();
            $data = [];
            $totalRatingAvg = 0;

            for ($i = 1; $i <= 5; $i++) {
                $data["total_rating_star_{$i}"] = collect($rating)->where("rating", $i)->count();
                $data["total_rating_star_{$i}_percent"] = round(($data["total_rating_star_{$i}"] / $totalRating * 100), 2);

                $totalRatingAvg += $data["total_rating_star_{$i}"] * $i;
            }

            $totalRatingAvg = round(($totalRatingAvg / $totalRating), 1);

            $data = array_merge([
                "total_rating" => $totalRating,
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
            $params = [];

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
                $review->mapping();
            }

            return $this->sendPaginationResponse($productReviews);
        } catch (\Exception $exception) {
            $erros = $exception->getMessage() . " - Line: " . $exception->getLine() . " - File: " . $exception->getFile();
            Log::error($erros);

            return $this->sendError("Have an error. Please try again", 500);
        }
    }

    public function helpfulReview(Request $request)
    {
        try {
            $user = auth()->guard("api")->user();
            $reviewId = $request->get("review_id");

            $helpfulReview = $this->reviewService->helpfulReview($reviewId, $user->id);

            return $this->sendResponse(empty($helpfulReview));
        } catch (\Exception $exception) {
            Log::error("{$exception->getMessage()}- Line: {$exception->getLine()} - File: {$exception->getFile()}");

            return $this->sendError("Have an error. Please try again", 500);
        }
    }

    public function sendReview(Request $request)
    {
        try {
            $data = [];

            $user = auth()->guard("api")->user();
            $data['user_id'] = $user->id;
            $data['product_id'] = $request->get("product_id");
            $data['rating'] = $request->get("rating");
            $data['images'] = [];
            if (empty($data['product_id'])) return $this->sendError("Product id is required", 400);
            if (!intval($data['product_id'])) return $this->sendError("Product id must be number", 400);
            if (intval($data['product_id']) < 1) return $this->sendError("Product id must be positive number", 400);


            if (empty($data['rating'])) return $this->sendError("Rating is required", 400);
            if (!intval($data['rating'])) return $this->sendError("Rating must be number", 400);
            if (intval($data['rating']) < 1) return $this->sendError("Rating must be positive number", 400);

            if (!empty($request->get("content"))) $data['content'] = $request->get("content");

            if (Str::length($data['content']) > 255) return $this->sendError("Content must be <= 255 characters", 400);

            if ($request->hasFile("images")) {
                $image = $request->file("images");
                $typeFileAccepts = config("generate.file_type_accept.image");

                if (!in_array($image->getClientOriginalExtension(), $typeFileAccepts)) return $this->sendError("Image type is not accept", 400);
                if ($image->getSize() > 10 * 1024 * 1024) return $this->sendError("Image size must be <= 10mb", 400);

                $file = $image->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $filename = Str::slug($filename) . "-" . time() . "." . $extension;
                $filePath = $this->uploadFileService->uploadFile($image, $filename, config("generate.file_storage_directory.image"));

                if (empty($filePath)) return $this->sendError("Can not upload your image", 400);

                $data['images'][] = $filePath;
            }

            if (!empty($data['images'])) {
                $data['images'] = implode(",", $data['images']);
            }

            $review = $this->reviewService->sendReview($data);

            $review->mapping(false);

            return $this->sendResponse($review);
        } catch
        (\Exception $exception) {
            Log::error("{$exception->getMessage()}- Line: {$exception->getLine()} - File: {$exception->getFile()}");

            return $this->sendError("Have an error. Please try again", 500);
        }
    }
}
