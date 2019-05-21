<?php

use App\Http\Controllers\Admin\ConvertToUtf8;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiskUsageController;
use App\Http\Controllers\Admin\ErrorLogController;
use App\Http\Controllers\Admin\FailedJobsController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\FileJobsController;
use App\Http\Controllers\Admin\ShowErrorLog;
use App\Http\Controllers\Admin\ShowPhpInfo;
use App\Http\Controllers\Admin\StoredFilesController;
use App\Http\Controllers\Admin\SubIdxController;
use App\Http\Controllers\Admin\SupController;
use App\Http\Controllers\Admin\ToolsController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

Route::delete('/error-log', [ErrorLogController::class, 'delete'])->name('errorLog.delete');

Route::delete('/feedback', [FeedbackController::class, 'delete'])->name('feedback.delete');

Route::delete('/failed-jobs/truncate', [FailedJobsController::class, 'truncate'])->name('failedJobs.truncate');

Route::get('/sup', [SupController::class, 'index'])->name('sup.index');

Route::get('/disk-usage', [DiskUsageController::class, 'index'])->name('diskUsage.index');

Route::get('/debug-tools', [ToolsController::class, 'index'])->name('tools.index');

Route::get('/stored-file/{storedFile}', [StoredFilesController::class, 'show'])->name('storedFiles.show');
Route::post('/stored-file/download', [StoredFilesController::class, 'download'])->name('storedFiles.download');
Route::delete('/stored-file/delete', [StoredFilesController::class, 'delete'])->name('storedFiles.delete');

Route::get('/file-jobs', [FileJobsController::class, 'index'])->name('fileJobs.index');

Route::get('/sub-idx', [SubIdxController::class, 'index'])->name('subIdx.index');

Route::post('/convert-to-utf8', ConvertToUtf8::class)->name('convertToUtf8');
Route::get('/phpinfo', ShowPhpInfo::class)->name('showPhpinfo');
Route::get('/error-log', ShowErrorLog::class)->name('showErrorLog');
