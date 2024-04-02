<?php

use App\Http\Controllers\Api\Banner\BannerController;
use App\Http\Controllers\Api\Brand\BrandController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("login", [UserController::class, "login"]);
Route::post("register", [UserController::class, "register"]);
Route::post("register-social", [UserController::class, "registerBySocial"]);
Route::post("forgot-password", [UserController::class, "forgotPassword"]);
Route::post("verify-reset-token", [UserController::class, "verifyTokenReset"]);
Route::post("reset-password", [UserController::class, "resetPassword"]);

Route::prefix("user")->middleware(["checkAuthApi"])->group(function () {
    Route::get("info", [UserController::class, 'info']);
    Route::post("update-info", [UserController::class, 'updateInfo']);
    Route::get("logout", [UserController::class, "logout"]);
    Route::post("update-password", [UserController::class, "updatePassword"]);
    Route::post("update-avatar", [UserController::class, 'updateAvatar']);
});

Route::get("get-home-banner", [BannerController::class, "getHomeBanner"]);
Route::get("get-category", [CategoryController::class, "getList"]);
Route::get("get-category-by-id/{id}", [CategoryController::class, "findById"]);

Route::prefix("review")->middleware(["checkAuthApi"])->group(function () {
    Route::get("get-product-rating", [ReviewController::class, 'getProductRating']);
    Route::get("get-product-review", [ReviewController::class, 'getProductReviews']);
    Route::post("helpful-review", [ReviewController::class, 'helpfulReview']);
    Route::post("send-review", [ReviewController::class, 'sendReview']);
});

Route::get("get-product", [ProductController::class, "getProduct"]);
Route::get("get-brand", [BrandController::class, "getBrand"]);
