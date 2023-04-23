<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user/{id}', [AuthController::class, 'getUser']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

//Protected Routes 
Route::prefix('products')->group(function () {
    Route::get('/all', [ProductController::class, 'all']);
    Route::post('/add', [ProductController::class, 'add']);
    Route::get('/get/{id}', [ProductController::class, 'get']);
    Route::put('/update/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
});
