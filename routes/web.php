<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TempFileController;

Route::get(
    '/',
    fn () => redirect('/temp')
);

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
| CSV Export
|--------------------------------------------------------------------------
*/

Route::get(
    '/temp/export-csv',
    [TempFileController::class, 'exportCsv']
)->name('temp.export.csv');

/*
|--------------------------------------------------------------------------
| JSON Export
|--------------------------------------------------------------------------
*/

Route::get(
    '/temp/export-json',
    [TempFileController::class, 'exportJson']
)->name('temp.export.json');

/*
|--------------------------------------------------------------------------
| Delete Single Activity
|--------------------------------------------------------------------------
*/

Route::delete(
    '/temp/activity/{id}',
    [TempFileController::class, 'delete']
)->name('temp.activity.delete');

/*
|--------------------------------------------------------------------------
| Delete Filtered Activities
|--------------------------------------------------------------------------
*/

Route::delete(
    '/temp/activity/delete-filtered',
    [TempFileController::class, 'deleteFiltered']
)->name('temp.activity.delete.filtered');

/*
|--------------------------------------------------------------------------
| Delete Failed Activities
|--------------------------------------------------------------------------
*/

Route::delete(
    '/temp/activity/delete-failed',
    [TempFileController::class, 'deleteFailed']
)->name('temp.activity.delete.failed');

/*
|--------------------------------------------------------------------------
| Temporary Storage Cleanup
|--------------------------------------------------------------------------
*/

Route::post(
    '/temp/cleanup',
    [TempFileController::class, 'cleanup']
)->name('temp.cleanup');