<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', 'products')->name('dashboard');
});

require __DIR__.'/products.php';
require __DIR__.'/pixs.php';
require __DIR__.'/settings.php';
