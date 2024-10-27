<?php


use App\Http\Controllers\Api\V1\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('refresh', [AuthenticationController::class, 'refresh']);
    Route::post('forget-password', [AuthenticationController::class, 'forgetPassword']);
    Route::post('reset-password', [AuthenticationController::class, 'resetPassword']);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('logout', [AuthenticationController::class, 'logout']);
        Route::post('me', [AuthenticationController::class, 'me']);
    });
});
