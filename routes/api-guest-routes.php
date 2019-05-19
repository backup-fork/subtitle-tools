<?php

use App\Http\Controllers\Api\FileGroupArchiveController;
use App\Http\Controllers\Api\FileGroupController;
use App\Http\Controllers\Api\SubIdxController;
use App\Http\Controllers\Api\SupJobController;

Route::get('sub-idx/{urlKey}/languages', [SubIdxController::class, 'languages'])->name('subIdx.languages');
Route::post('sub-idx/{urlKey}/languages/{languageId}', [SubIdxController::class, 'extractLanguage'])->name('subIdx.post');

Route::get('file-group/result/{urlKey}', [FileGroupController::class, 'show'])->name('fileGroup.result');

Route::get('file-group/archive/{urlKey}', [FileGroupArchiveController::class, 'show'])->name('fileGroupArchive.show');
Route::post('file-group/archive/request/{urlKey}', [FileGroupArchiveController::class, 'request'])->name('fileGroupArchive.request');

Route::get('sup-job/{urlKey}', [SupJobController::class, 'show'])->name('supJob.show');
