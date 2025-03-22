<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('products', function () {
    return Inertia::render('product/index'); // Sesuai dengan nama file di `pages`
})->name('products');

Route::get('suppliers', function () {
    return Inertia::render('supplier/index'); // Sesuai dengan nama file di `pages`
})->name('suppliers');

Route::get('customers', function () {
    return Inertia::render('customer/index'); // Sesuai dengan nama file di `pages`
})->name('customers');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//route yang pake resource
Route::middleware(['auth','verified'])->group(function (){
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
