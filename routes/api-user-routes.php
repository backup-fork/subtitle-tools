<?php

use App\Http\Controllers\User\Api\SubIdxBatchResult;

Route::get('/sub-idx-batch/{subIdxBatch}/result', SubIdxBatchResult::class)->name('subIdxBatch.result');
