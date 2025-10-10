<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CarController;

Route::get('/', function () {
    return view('welcome');
});

//Auth
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');

//Home User 
Route::get('/home', fn() => view('user.home.index'))->name('home');
Route::get('/car', fn() => view('user.car.index'))->name('car');
Route::get('/car/show', [CarController::class, 'show'])->name('car.show');
