<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/home');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
