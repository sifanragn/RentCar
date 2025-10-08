<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');


