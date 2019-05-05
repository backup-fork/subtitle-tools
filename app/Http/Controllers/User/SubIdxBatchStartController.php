<?php

namespace App\Http\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Http\Request;

class SubIdxBatchStartController
{
    public function index(SubIdxBatch $subIdxBatch)
    {
        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-start', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }

    public function post(Request $request)
    {
        dd($request);
    }
}
