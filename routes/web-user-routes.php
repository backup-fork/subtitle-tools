<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\SubIdxBatchController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/sub-idx-batches',               [SubIdxBatchController::class, 'index'])->name('subIdxBatch.index');
Route::get('/sub-idx-batches/create',        [SubIdxBatchController::class, 'create'])->name('subIdxBatch.create');
Route::post('/sub-idx-batches/create',       [SubIdxBatchController::class, 'store'])->name('subIdxBatch.store');
Route::get('/sub-idx-batches/{subIdxBatch}', [SubIdxBatchController::class, 'show'])->name('subIdxBatch.show');
