<?php

use App\Http\Controllers\APIs\BookController;
use App\Http\Controllers\APIs\BorrowRecordController;
use App\Http\Controllers\APIs\Auth\AuthController;
use App\Http\Controllers\APIs\CategoryController;
use App\Http\Controllers\APIs\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('user/profile', 'show');
        Route::put('user/profile', 'updateProfile');
        Route::put('user/change-password', 'changePassword');
        Route::post('logout', 'logout');
        Route::delete('user/delete', 'deleteUser');
    });
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('books', BookController::class)->except(['index', 'show']);
        Route::apiResource('borrowRecords', BorrowRecordController::class)->except(['index', 'show']);
        Route::post('borrowRecords/due/{id}', [BorrowRecordController::class, 'due']);
        Route::apiResource('ratings', RatingController::class)->except(['show']);
    });
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    Route::apiResource('books', BookController::class)->only(['index', 'show']);
    Route::apiResource('borrowRecords', BorrowRecordController::class)->only(['index', 'show']);
    Route::apiResource('ratings', RatingController::class)->only(['show']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
