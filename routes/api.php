<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Api\DuitkuCallbackController;
use App\Http\Controllers\Api\PaymentGatewayController;


// 🧾 Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔐 Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // contoh tambahan API data
     Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{id}', [CarController::class, 'show']);
});


Route::post('/payment/create', [PaymentGatewayController::class, 'createPayment']);
