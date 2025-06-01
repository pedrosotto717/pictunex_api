
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/token', [AuthController::class, 'token']);
    Route::post('/verify', [AuthController::class, 'verify']);
    Route::post('/update', [AuthController::class, 'update'])->middleware('jwt.auth');
});

// Image routes
Route::prefix('images')->group(function () {
    // Public routes
    Route::get('/', [ImageController::class, 'index']);
    Route::get('/categories', [ImageController::class, 'categories']);
    Route::get('/category/{category}', [ImageController::class, 'byCategory']);
    Route::get('/search', [ImageController::class, 'search']);
    Route::get('/{id}', [ImageController::class, 'show']);
    
    // Protected routes
    Route::middleware('jwt.auth')->group(function () {
        Route::post('/', [ImageController::class, 'store']);
        Route::post('/{id}/update', [ImageController::class, 'update']);
        Route::delete('/{id}/delete', [ImageController::class, 'destroy']);
    });
});
