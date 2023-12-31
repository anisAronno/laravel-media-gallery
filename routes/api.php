<?php

use AnisAronno\MediaGallery\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function ()
{
    Route::get('image', [ImageController::class, 'index'])->name('image.index');
    Route::get('image/{id}', [ImageController::class, 'show'])->name('image.show');
    Route::post('image', [ImageController::class, 'store'])->name('image.store');
    Route::delete('image/{id}', [ImageController::class, 'destroy'])->name('image.destroy');

    Route::post('/image/update/{id}', [ImageController::class, 'update'])->name('image.update');
    Route::post('image/batch-delete', [ImageController::class, 'batchDelete'])->name('image.batch.delete');
});
