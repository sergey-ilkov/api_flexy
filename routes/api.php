<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\ApiTestController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CallController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::prefix('/v1')->group(function () {
    Route::get('/', [ApiTestController::class, 'index'])->name('api.home');
});


Route::prefix('/v1/auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {

        // Route::get('/', [AuthController::class, 'getUser'])->name('auth.user');

        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });


    Route::post('/refresh', [AuthController::class, 'refreshToken'])->name('auth.refresh');


    // ? auth header Authorization Bearer token api route
    // Route::middleware('auth:sanctum', 'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value)->group(function () {
    //     Route::post('/refresh', [AuthController::class, 'refreshToken'])->name('auth.refresh');
    // });

});

Route::middleware('auth:sanctum')->prefix('v1/task')->group(function () {
    Route::post('/', [CallController::class, 'task'])->name('task.create');
});