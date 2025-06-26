<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/users', function () {
    return view('users.index');
});

Route::get('/products', function () {
    return view('products.index');
});

Route::get('/orders', function () {
    return view('orders.index');
});