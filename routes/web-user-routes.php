<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\SubIdxBatchController;
use App\Http\Controllers\User\SubIdxBatchLink;
use App\Http\Controllers\User\SubIdxBatchUpload;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/sub-idx-batches',                        [SubIdxBatchController::class, 'index'])->name('subIdxBatch.index');
Route::get('/sub-idx-batches/create',                 [SubIdxBatchController::class, 'create'])->name('subIdxBatch.create');
Route::post('/sub-idx-batches/create',                [SubIdxBatchController::class, 'store'])->name('subIdxBatch.store');
Route::get('/sub-idx-batches/{subIdxBatch}',          [SubIdxBatchController::class, 'showUpload'])->name('subIdxBatch.showUpload')->middleware('can:access,subIdxBatch');
Route::get('/sub-idx-batches/{subIdxBatch}/unlinked', [SubIdxBatchController::class, 'showUnlinked'])->name('subIdxBatch.showUnlinked')->middleware('can:access,subIdxBatch');
Route::get('/sub-idx-batches/{subIdxBatch}/files',    [SubIdxBatchController::class, 'showLinked'])->name('subIdxBatch.showLinked')->middleware('can:access,subIdxBatch');

Route::post('/sub-idx-batches/{subIdxBatch}/upload', SubIdxBatchUpload::class)->name('subIdxBatch.upload')->middleware('can:access,subIdxBatch');

Route::post('/sub-idx-batches/{subIdxBatch}/link', SubIdxBatchLink::class)->name('subIdxBatch.link')->middleware('can:access,subIdxBatch');
