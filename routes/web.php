<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\PreventBackHistory;

// ===== AUTH CONTROLLERS =====
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

// ===== ADMIN CONTROLLERS =====
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ManageAdminController;
use App\Http\Controllers\Admin\CarAdminController;
use App\Http\Controllers\Admin\UserVerificationController;
use App\Http\Controllers\Admin\RentalAdminController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\DriverAdminController;

// ===== USER CONTROLLERS =====
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\CarController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\RentalController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\UserVerifikasiController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PickupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== DEBUG / TEST ==================== //
Route::get('/test-log', function () {
    \Log::error('🚨 Laravel log test berhasil!');
    return 'Cek terminal/log kamu 😉';
});
Route::get('/test-view', fn() => view('admin.merek.index', ['brands' => []]));
Route::get('/check-auth', fn() => auth()->check()
    ? '✅ Login sebagai: ' . auth()->user()->email
    : '❌ Belum login'
);

// ==================== AUTH (LOGIN / REGISTER / LOGOUT) ==================== //
Route::middleware(['guest', PreventBackHistory::class])->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');

    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Logout
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// ==================== PUBLIC (TANPA LOGIN) ==================== //
Route::get('/', fn() => redirect()->route('home'));
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/cars', [CarController::class, 'index'])->name('user.cars.index');
Route::get('/cars/{id}', [CarController::class, 'show'])->name('user.cars.show');

// Hubungi Kami
Route::get('/hubungi-kami', fn() => view('user.kontak.index'))->name('user.kontak.index');

// Halaman guest
Route::get('/user/car/guest', fn() => view('user.car.guest'))->name('user.car.guest');
Route::get('/profile', fn() => view('user.profile.guest'))->name('user.profile.guest');
Route::get('/payments', fn() => view('user.payments.guest'))->name('user.payments.guest');

// ==================== USER AREA (LOGIN WAJIB) ==================== //
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Verifikasi
    Route::get('/verifikasi', [UserVerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi', [UserVerifikasiController::class, 'store'])->name('verifikasi.store');

    // Mobil
    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::get('/cars/{id}', [CarController::class, 'show'])->name('cars.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/hapus/{tipe}', [ProfileController::class, 'hapusVerifikasi'])->name('profile.hapusVerifikasi');
    Route::post('/profile/verify-password', [ProfileController::class, 'verifyPassword'])->name('profile.verifyPassword');

    // Rental
    Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/create/{car_id}', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals/store/{car_id}', [RentalController::class, 'store'])->name('rentals.store');
    Route::post('/rentals/cancel-latest', [RentalController::class, 'cancelLatest'])->name('rentals.cancelLatest');
    Route::get('/rentals/{id}', [RentalController::class, 'show'])->name('rentals.show');

    // 💳 Pembayaran
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/detail-rental/{rental_id}', [PaymentController::class, 'detailRental'])->name('payments.detailRental');
    Route::post('/payments/start/{rental_id}', [PaymentController::class, 'startProcess'])->name('payments.start');
    Route::get('/payments/process/{payment_id}', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/payments/cancel-soft/{payment_id}', [PaymentController::class, 'cancelSoft'])->name('payments.cancelSoft');
    Route::get('/payments/show/{payment_id}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/check-expired', [PaymentController::class, 'checkExpired'])->name('payments.checkExpired');
    Route::get('/payments/check-status/{payment_id}', [PaymentController::class, 'checkStatus'])->name('payments.checkStatus');
    Route::get('/payments/continue/{payment_id}', [PaymentController::class, 'continuePayment'])->name('payments.continue');
    Route::get('/payments/status-list', [PaymentController::class, 'statusList'])->name('payments.statusList');
    Route::get('/payments/{id}/download', [PaymentController::class, 'downloadReceipt'])->name('payments.download');
    Route::get('/payments/{id}/json', [PaymentController::class, 'json'])->name('payments.json');

    // Ongkir / Pickup Distance
    Route::post('/pickup/distance', [PickupController::class, 'distance'])->name('pickup.distance');
});

// ==================== ADMIN AREA ==================== //
Route::prefix('admin')->middleware('admin.session')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

    // Kelola Admin
    Route::get('/kelola-admin', [ManageAdminController::class, 'index'])->name('manage.index');
    Route::get('/kelola-admin/create', [ManageAdminController::class, 'create'])->name('manage.create');
    Route::post('/kelola-admin', [ManageAdminController::class, 'store'])->name('manage.store');
    Route::get('/kelola-admin/{id}/edit', [ManageAdminController::class, 'edit'])->name('manage.edit');
    Route::post('/kelola-admin/{id}', [ManageAdminController::class, 'update'])->name('manage.update');
    Route::post('/kelola-admin/{id}/deactivate', [ManageAdminController::class, 'deactivate'])->name('manage.deactivate');

    // Mobil
    Route::get('cars/brands', [CarAdminController::class, 'brandIndex'])->name('cars.brands');
    Route::post('cars/brands', [CarAdminController::class, 'brandStore'])->name('cars.brands.store');
    Route::delete('cars/brands/{id}', [CarAdminController::class, 'brandDestroy'])->name('cars.brands.destroy');
    Route::get('cars/models', [CarAdminController::class, 'modelIndex'])->name('cars.models');
    Route::post('cars/models', [CarAdminController::class, 'modelStore'])->name('cars.models.store');
    Route::get('api/models/{brand_id}', [CarAdminController::class, 'getModelsByBrand']);
    Route::resource('cars', CarAdminController::class);

    // Verifikasi User
    Route::get('/users', [UserVerificationController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserVerificationController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/verify', [UserVerificationController::class, 'verify'])->name('users.verify');

    // Penyewaan (Admin)
    Route::get('/rentals', [RentalAdminController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/{id}', [RentalAdminController::class, 'show'])->name('rentals.show');
    Route::post('/rentals/{id}/update-status', [RentalAdminController::class, 'updateStatus'])->name('rentals.updateStatus');

    // Payments (Admin)
    Route::post('/payments/refresh/{id}', [AdminPaymentController::class, 'refresh'])->name('payments.refresh');

    // Invoices (Admin)
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{rental_id}/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices/{rental_id}/store', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{id}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::post('/invoices/{id}/manual-update', [InvoiceController::class, 'manualUpdate'])->name('invoices.manualUpdate');
    Route::post('/invoices/{id}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
    Route::post('/invoices/{id}/retry-payment', [InvoiceController::class, 'retryPayment'])->name('invoices.retryPayment');

    // Drivers
    Route::resource('drivers', DriverAdminController::class);

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
});

// ===== CALLBACK PAYMENT (DUITKU / SANDBOX) ===== //
Route::post('/api/payment/callback', [PaymentController::class, 'callback'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('payment.callback');