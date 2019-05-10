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
            'languages' => $subIdxBatch->batchFileLanguages(),
        ]);
    }

    public function post(Request $request, SubIdxBatch $subIdxBatch)
    {
        $availableLanguages = array_map(function ($array) {
            return $array[0];
        }, $subIdxBatch->batchFileLanguages());

        $request->validate([
            'languages' => 'required|array|min:1',
            'languages.*' => 'required|distinct|in:'.implode(',', $availableLanguages),
        ]);

        $languages = $request->get('languages');
    }
}
