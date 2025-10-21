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
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\User\ContactController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\UserVerifikasiController;
use App\Http\Controllers\Admin\LaporanController;

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


// ==================== AUTH (LOGIN / REGISTER / LOGOUT) ==================== //
Route::middleware(['guest', PreventBackHistory::class])->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');

    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Mobil
    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::get('/cars/{id}', [CarController::class, 'show'])->name('cars.show');

    // Profil
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // Rental
    Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/create/{car_id}', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals/store/{car_id}', [RentalController::class, 'store'])->name('rentals.store');
    Route::post('/rentals/cancel-latest', [RentalController::class, 'cancelLatest'])->name('rentals.cancelLatest');
    Route::get('/rentals/{id}', [RentalController::class, 'show'])->name('rentals.show');



    // 💳 Pembayaran
    Route::get('/payments/{id}/json', [PaymentController::class, 'json'])->name('payments.json');
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

    Route::get('/profile',        [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update',[ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/hapus/{tipe}', [ProfileController::class, 'hapusVerifikasi'])->name('profile.hapusVerifikasi');
    Route::post('/profile/verify-password', [ProfileController::class, 'verifyPassword'])->name('profile.verifyPassword');


    //ONGKIR
    Route::post('/pickup/distance', [\App\Http\Controllers\User\PickupController::class, 'distance'])
    ->name('pickup.distance');

    // 📞 Halaman Hubungi Kami
    Route::get('/kontak', [\App\Http\Controllers\User\ContactController::class, 'index'])->name('kontak.index');
    Route::post('/kontak', [\App\Http\Controllers\User\ContactController::class, 'store'])->name('kontak.store');

    Route::get('/payments/{payment_id}/download', [\App\Http\Controllers\User\PaymentController::class, 'downloadReceipt'])
    ->name('payments.download');

    Route::get('/verifikasi', [UserVerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi/store', [UserVerifikasiController::class, 'store'])->name('verifikasi.store');
});


// ==================== ADMIN AREA ==================== //
Route::prefix('admin')->middleware('admin.session')->group(function () {
 Route::post('/invoices/{invoice}/retry-payment', [App\Http\Controllers\Admin\InvoiceController::class, 'retryPayment'])
    ->name('admin.invoices.retryPayment');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::get('/invoices/{rental_id}/create', [InvoiceController::class, 'create'])->name('admin.invoices.create');
    Route::post('/invoices/{rental_id}/store', [InvoiceController::class, 'store'])->name('admin.invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('admin.invoices.show');
    Route::post('/admin/invoices/{id}/cancel', [InvoiceController::class, 'cancel'])->name('admin.invoices.cancel');
    
    
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
Route::delete('cars/brands/{id}', [CarAdminController::class, 'brandDestroy'])->name('cars.brands.destroy');

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
    

Route::post('/payments/refresh/{id}', [AdminPaymentController::class, 'refresh'])
    ->name('admin.payments.refresh');
    Route::post('/invoices/{id}/update-status', [InvoiceController::class, 'updateStatus'])
    ->name('admin.invoices.updateStatus');
    Route::post('/admin/invoices/manual-update/{id}', [InvoiceController::class, 'manualUpdate'])
    ->name('admin.payments.manualUpdate');

    Route::get('/laporan', [App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('admin.laporan.index');
Route::get('/laporan/cetak', [App\Http\Controllers\Admin\LaporanController::class, 'cetak'])->name('admin.laporan.cetak');



});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
