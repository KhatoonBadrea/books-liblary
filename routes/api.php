<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BorrowRecordController;
use App\Http\Controllers\Api\RatingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});
Route::get('book', [BookController::class, 'index']); // Public route for index
Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('book', BookController::class)->except(['index']);
    Route::apiResource('record', BorrowRecordController::class);
    Route::post('return', [BorrowRecordController::class, 'returnBook']);
    Route::apiResource('rating', RatingController::class);
});

Route::apiResource('user', UserController::class);
