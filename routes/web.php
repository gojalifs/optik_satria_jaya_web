<?php

use App\Http\Controllers\SJO\InvoiceController;
use App\Http\Controllers\SJO\PrivacayPolicyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::middleware(['basic.auth'])->group(function () {
    Route::get('invoice/create', function () {
        return Inertia::render('invoice/create');
    })->name('invoice.create');
});

// Route::middleware(['signature_verification'])->group(function(){
Route::post('test', [InvoiceController::class, 'test']);
// });

Route::get('privacy', [PrivacayPolicyController::class, 'index'])->name('privacy');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
