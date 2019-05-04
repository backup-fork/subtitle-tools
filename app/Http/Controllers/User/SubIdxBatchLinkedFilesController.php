<?php

namespace App\Http\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use Illuminate\Http\Request;

class SubIdxBatchLinkedFilesController
{
    public function index(SubIdxBatch $subIdxBatch)
    {
        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-linked', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }

    public function unlink(Request $request, SubIdxBatchFile $subIdxBatchFile)
    {

    }
}
