<?php

use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

//http://127.0.0.1:8000/api/v1/users?sort=-id&includes=roles&show=2&page=3&filter['name']=safayat

require 'auth.php';

Route::middleware(['jwt.auth'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
});

