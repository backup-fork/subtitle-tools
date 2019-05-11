<?php

namespace App\Http\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;

class SubIdxBatchDetailsController
{
    public function index(SubIdxBatch $subIdxBatch)
    {
        if (! $subIdxBatch->started_at) {
            return redirect()->route('user.subIdxBatch.showUpload', $subIdxBatch);
        }

        return view('user.sub-idx-batch.show', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }
}
