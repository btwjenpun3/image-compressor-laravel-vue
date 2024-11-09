<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageUploadController;
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->name('home.')
    ->controller(HomeController::class)
    ->group(function() {
        Route::get('/', 'index')->name('index');
    });

Route::prefix('/upload')
    ->name('upload.')
    ->controller(ImageUploadController::class)
    ->group(function() {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'upload')->name('upload');
    });
