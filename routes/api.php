<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1'], function(){
    Route::middleware(['auth:sanctum'])->group(function(){
        Route::group(['prefix' => '/forms'], function(){
            Route::apiResource('/', FormController::class)->except('show');
            Route::get('/{form_slug}', [FormController::class, 'show']);
            Route::apiResource('/{form_slug}/questions', QuestionController::class);
            Route::apiResource('/{form_slug}/responses', ResponseController::class);

        });
        Route::get('auth/me', [AuthController::class, 'index']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
    });
        Route::post('auth/login', [AuthController::class, 'login']);
});
