<?php

use App\Http\Controllers\SJO\InvoiceController;
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

// Route::middleware(['signature_verification'])->group(function(){
    Route::post('test', [InvoiceController::class, 'test']);
// });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
