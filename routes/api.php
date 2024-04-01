<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\JobAnswerController;

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
Route::controller(AuthController::class)->group(function() {
    Route::post('/login', 'login');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('vacancies')->group(function () {
    Route::get('/', [JobVacancyController::class, 'index']);

    Route::middleware('auth:sanctum')->group( function () {
        Route::post('/create', [JobVacancyController::class, 'create']);
        Route::post('/update/{id}', [JobVacancyController::class, 'update']);
        Route::delete('/delete/{id}', [JobVacancyController::class, 'delete']);
        Route::post('/answer/{id}', [JobAnswerController::class, 'create']);
        Route::delete('/answer/delete/{job_id}', [JobAnswerController::class, 'delete']);
    });
});
