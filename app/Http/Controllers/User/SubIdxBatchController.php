<?php

namespace App\Http\Controllers\User;

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
}
