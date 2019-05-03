<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\SubIdxBatchController;
use App\Http\Controllers\User\SubIdxBatchUpload;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/sub-idx-batches',                        [SubIdxBatchController::class, 'index'])->name('subIdxBatch.index');
Route::get('/sub-idx-batches/create',                 [SubIdxBatchController::class, 'create'])->name('subIdxBatch.create');
Route::post('/sub-idx-batches/create',                [SubIdxBatchController::class, 'store'])->name('subIdxBatch.store');
Route::get('/sub-idx-batches/{subIdxBatch}',          [SubIdxBatchController::class, 'showUpload'])->name('subIdxBatch.showUpload');
Route::get('/sub-idx-batches/{subIdxBatch}/unlinked', [SubIdxBatchController::class, 'showUnlinked'])->name('subIdxBatch.showUnlinked');
Route::get('/sub-idx-batches/{subIdxBatch}/files',    [SubIdxBatchController::class, 'showLinked'])->name('subIdxBatch.showLinked');

Route::post('/sub-idx-batches/{subIdxBatch}/upload', SubIdxBatchUpload::class)->name('subIdxBatch.upload')->middleware('can:access,subIdxBatch');
