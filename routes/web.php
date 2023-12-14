<?php

use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Web\CrawlerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("verify_email", [UserController::class, 'verifyTokenEmail']);
Route::get("crawl-category", [CrawlerController::class, "crawlCategory"]);
Route::get("crawl-product", [CrawlerController::class, "crawlProductView"]);
Route::post("crawl-product", [CrawlerController::class, "crawlProductHandle"]);
