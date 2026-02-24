<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TempFileController;

// Redirect homepage to temporary directory demo page
Route::get('/', fn () => redirect('/temp'));

// Show main demo UI page
Route::get('/temp', [TempFileController::class,'index'])->name('temp.index');

// Create temporary file and show success message
Route::get('/temp/create', [TempFileController::class,'createTemp'])
    ->name('temp.create');

// Download generated temporary file
Route::get('/temp/download', [TempFileController::class,'downloadTempFile'])
    ->name('temp.download');

// Create ZIP file from temp files and download it
Route::get('/temp/zip', [TempFileController::class,'createZip'])
    ->name('temp.zip');