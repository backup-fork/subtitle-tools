<?php

namespace App\Http\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubIdxBatchController
{
    public function index()
    {
        return view('user.sub-idx-batch.index', [
            'subIdxBatches' => user()->subIdxBatches
        ]);
    }

    public function create()
    {
        return view('user.sub-idx-batch.create');
    }

    public function store(Request $request)
    {
        $values = $request->validate([
            'max_files' => 'required|numeric',
        ]);

        $subIdxBatch = user()->subIdxBatches()->create(['id' => Str::uuid()] + $values);

        return redirect()->route('user.subIdxBatch.showUpload', $subIdxBatch);
    }

    public function showUpload(SubIdxBatch $subIdxBatch)
    {
        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-uploads', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }

    public function showUnlinked(SubIdxBatch $subIdxBatch)
    {
        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-unlinked', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }

    public function showLinked(SubIdxBatch $subIdxBatch)
    {
        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-linked', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }
}
