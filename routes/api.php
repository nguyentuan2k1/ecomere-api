<?php

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
Route::post("forgot-password", [UserController::class, "forgotPassword"]);

Route::prefix("user")->middleware(["checkAuthApi"])->group( function (){
    Route::get("info", [UserController::class, 'info']);
    Route::post("update-info", [UserController::class, 'updateInfo']);
    Route::get("logout", [UserController::class, "logout"]);
    Route::post("update-password", [UserController::class, "updatePassword"]);
    Route::post("update-avatar", [UserController::class, 'updateAvatar']);
});
