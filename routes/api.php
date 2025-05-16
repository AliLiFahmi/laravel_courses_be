<?php

// controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TaskController;
// illuminate
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// routes
Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        // courses
        Route::apiResource('courses', CourseController::class);

        // tasks
        Route::apiResource('tasks', TaskController::class);

        // documents
        Route::apiResource('documents', DocumentController::class);
    });
});
