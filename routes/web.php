<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/home');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::post('columns/{column}/move/{direction}', [OrderController::class, 'column']);

    Route::resource('columns', ColumnController::class, ['only' => ['index', 'store', 'destroy']]);
    Route::resource('columns.cards', CardController::class, ['only' => ['store', 'destroy']]);
});
