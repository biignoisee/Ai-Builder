<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/agendar-cita', 'appointment')
    ->name('appointment');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');
});

require __DIR__.'/settings.php';
