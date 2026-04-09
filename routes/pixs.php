<?php

use App\Http\Controllers\PixController;
use Illuminate\Support\Facades\Route;

Route::resource('pixs', PixController::class)
->parameters([
    'pixs' => 'pixOption'
])
->only(['index', 'create', 'edit'])
->middleware(['auth', 'verified']);

