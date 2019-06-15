<?php

namespace App\Http\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubIdxBatchController
{
    public function index()
    {
        return view('user.sub-idx-batch.index', [
            'user' => $user = user(),
            'subIdxBatches' => $user->subIdxBatches->sortByDesc('created_at'),
        ]);
    }

    public function store()
    {
        $user = user();

        if ($user->batch_tokens_left === 0) {
            abort(422);
        }

        $subIdxBatch = $user->subIdxBatches()
            ->create([
                'id' => Str::uuid(),
                'label' => user()->subIdxBatches()->count() + 1,
            ]);

        return redirect()->route('user.subIdxBatch.showUpload', $subIdxBatch);
    }

    public function delete(SubIdxBatch $subIdxBatch)
    {
        abort_if($subIdxBatch->started_at, 422);

        $subIdxBatch->delete();

        Storage::deleteDirectory("sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id");

        return redirect()->route('user.subIdxBatch.index');
    }
}
