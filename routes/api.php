<?php

use AnisAronno\MediaGallery\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::resource('image', ImageController::class, ['except' => ['update']]);
    Route::post('/image/update/{image}', [ImageController::class, 'update'])->name('image.update');
    Route::post('image/delete-all', [ImageController::class, 'groupDelete'])->name('image.destroy.all');
});