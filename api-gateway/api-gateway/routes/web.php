<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Admin routes (will be protected by middleware later)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/users', function () {
    return view('admin.users');
})->name('admin.users');

// Creator routes
Route::get('/creator/dashboard', function () {
    return view('creator.dashboard');
})->name('creator.dashboard');

Route::get('/creator/products', function () {
    return view('creator.products');
})->name('creator.products');

// Buyer routes  
Route::get('/buyer/dashboard', function () {
    return view('buyer.dashboard');
})->name('buyer.dashboard');

Route::get('/buyer/orders', function () {
    return view('buyer.orders');
})->name('buyer.orders');

// Management routes (accessible by admins and appropriate roles)
Route::get('/users', function () {
    return view('users.index');
})->name('users.index');

Route::get('/products', function () {
    return view('products.index');
})->name('products.index');

Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');

// Browse products page for public
Route::get('/browse', function () {
    return view('products.browse');
})->name('products.browse');

// Legacy routes (redirect to appropriate dashboards)
Route::get('/dashboard', function () {
    return redirect('/admin/dashboard');
});