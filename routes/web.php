<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Bridge routes for welcome.blade.php compatibility
Route::get('/login', fn() => redirect('/admin/login'))->name('login');
Route::get('/register', fn() => redirect('/admin/register'))->name('register');
Route::get('/dashboard', fn() => redirect('/admin'))->name('dashboard');
