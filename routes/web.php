<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TempFileController;

Route::get('/', fn () => redirect('/temp'));

Route::get(
    '/temp',
    [TempFileController::class, 'index']
)->name('temp.index');

Route::get(
    '/temp/create',
    [TempFileController::class, 'createTemp']
)->name('temp.create');

Route::get(
    '/temp/download',
    [TempFileController::class, 'downloadTempFile']
)->name('temp.download');

Route::get(
    '/temp/zip',
    [TempFileController::class, 'createZip']
)->name('temp.zip');

/*
|--------------------------------------------------------------------------
| Temporary File Activity CSV Export
|--------------------------------------------------------------------------
*/

Route::get(
    '/temp/export-csv',
    [TempFileController::class, 'exportCsv']
)->name('temp.export.csv');

/*
|--------------------------------------------------------------------------
| Temporary Storage Cleanup
|--------------------------------------------------------------------------
*/

Route::post(
    '/temp/cleanup',
    [TempFileController::class, 'cleanup']
)->name('temp.cleanup');