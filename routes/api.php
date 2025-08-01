<?php

use App\Http\Controllers\SJO\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('transaction', [InvoiceController::class, 'create']);

Route::get('/pdf/{id}', [InvoiceController::class, 'download'])
    ->name('pdf.download')
    ->middleware(['signed', 'throttle:5,1']);