<?php

use AnisAronno\MediaGallery\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function ()
{
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::get('media/{id}', [MediaController::class, 'show'])->name('media.show');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');

    Route::post('media/update/{id}', [MediaController::class, 'update'])->name('media.update');
    Route::post('media/batch-delete', [MediaController::class, 'batchDelete'])->name('media.batch.delete');
});
