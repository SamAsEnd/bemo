<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\ExportDbController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/home');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::post('columns/{column}/set', [OrderController::class, 'setColumn']);
    Route::post('columns/{column}/cards/{card}/set', [OrderController::class, 'setCard']);

    Route::post('columns/{column}/move/{direction}', [OrderController::class, 'column']);
    Route::post('columns/{column}/cards/{card}/move/{direction}', [OrderController::class, 'card']);

    Route::resource('columns', ColumnController::class, ['only' => ['index', 'store', 'destroy']]);
    Route::resource('columns.cards', CardController::class, ['only' => ['store', 'update', 'destroy']]);

    Route::get('export-db', [ExportDbController::class, 'export'])->name('export-db');
});
