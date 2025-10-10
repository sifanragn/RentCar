<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\ManageAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CarAdminController;
use App\Http\Controllers\Admin\UserVerificationController;
use App\Http\Controllers\Admin\RentalAdminController;
use App\Http\Controllers\User\CarController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\RentalController;
use App\Http\Controllers\User\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Semua route frontend (user) dan backend (admin)
|--------------------------------------------------------------------------
*/

// ==================== LANDING PAGE ==================== //
Route::get('/', function () {
    return view('landingpage');
})->name('landingpage');

// ==================== DEBUG / TEST ==================== //
Route::get('/test-log', function () {
    \Log::error('🚨 Laravel log test berhasil!');
    return 'Cek terminal/log kamu 😉';
});

Route::get('/test-view', function () {
    return view('admin.merek.index', ['brands' => []]);
});

Route::get('/check-auth', function () {
    return auth()->check()
    ? '✅ Login sebagai: ' . auth()->user()->email
    : '❌ Belum login';
});

// ==================== AUTH (LOGIN / REGISTER / LOGOUT) ==================== //
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// ==================== USER AREA ==================== //
Route::prefix('user')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->name('user.dashboard');
    
    // Mobil
    Route::get('/cars', [CarController::class, 'index'])->name('user.cars.index');
    Route::get('/cars/{id}', [CarController::class, 'show'])->name('user.cars.show');
    
    // Profil User - Upload KTP & KK
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('user.profile.update');
    
    
    
    // Rental
    Route::get('rentals', [RentalController::class, 'index'])->name('user.rentals.index');
    Route::get('rentals/create/{car_id}', [RentalController::class, 'create'])->name('user.rentals.create');
    Route::post('rentals/store/{car_id}', [RentalController::class, 'store'])->name('user.rentals.store');
    Route::post('rentals/cancel-latest', [RentalController::class, 'cancelLatest'])->name('user.rentals.cancelLatest');
});


   Route::middleware('auth')->prefix('user')->name('user.')->group(function () {

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{rental_id}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/detail/{payment_id}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/process/{payment_id}', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/payments/cancel/{payment_id}', [PaymentController::class, 'cancel'])->name('payments.cancel');
    Route::get('/payments/check-expired', [PaymentController::class, 'checkExpired'])->name('payments.checkExpired');
});





// ==================== ADMIN AREA ==================== //
Route::prefix('admin')->middleware('admin.session')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard.index');

    // 👤 Kelola Admin
    Route::get('/kelola-admin', [ManageAdminController::class, 'index'])->name('admin.manage.index');
    Route::get('/kelola-admin/create', [ManageAdminController::class, 'create'])->name('admin.manage.create');
    Route::post('/kelola-admin', [ManageAdminController::class, 'store'])->name('admin.manage.store');
    Route::get('/kelola-admin/{id}/edit', [ManageAdminController::class, 'edit'])->name('admin.manage.edit');
    Route::post('/kelola-admin/{id}', [ManageAdminController::class, 'update'])->name('admin.manage.update');
    Route::post('/kelola-admin/{id}/deactivate', [ManageAdminController::class, 'deactivate'])->name('admin.manage.deactivate');

    // 🚘 CRUD Mobil (Admin)
    Route::get('cars/brands', [CarAdminController::class, 'brandIndex'])->name('cars.brands');
    Route::post('cars/brands', [CarAdminController::class, 'brandStore'])->name('cars.brands.store');

    Route::get('cars/models', [CarAdminController::class, 'modelIndex'])->name('cars.models');
    Route::post('cars/models', [CarAdminController::class, 'modelStore'])->name('cars.models.store');

    Route::get('api/models/{brand_id}', [CarAdminController::class, 'getModelsByBrand']);
    Route::resource('cars', CarAdminController::class);

    // 👥 Verifikasi User (Admin)
    Route::get('/users', [UserVerificationController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}', [UserVerificationController::class, 'show'])->name('admin.users.show');
    Route::post('/users/{id}/verify', [UserVerificationController::class, 'verify'])->name('admin.users.verify');

    // 📦 Penyewaan (Admin)
    Route::get('/rentals', [RentalAdminController::class, 'index'])->name('admin.rentals.index');
    Route::get('/rentals/{id}', [RentalAdminController::class, 'show'])->name('admin.rentals.show');
    Route::post('/rentals/{id}/update-status', [RentalAdminController::class, 'updateStatus'])->name('admin.rentals.updateStatus');
});
