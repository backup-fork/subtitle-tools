<?php

namespace App\Http\Controllers\User;

use App\Jobs\StartSubIdxBatchJob;
use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Http\Request;

class SubIdxBatchStartController
{
    public function index(SubIdxBatch $subIdxBatch)
    {
        if ($subIdxBatch->started_at) {
            return redirect()->route('user.subIdxBatch.show', $subIdxBatch);
        }

        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-start', [
            'user' => user(),
            'subIdxBatch' => $subIdxBatch,
            'languages' => $subIdxBatch->batchFileLanguageCount(),
        ]);
    }

    public function post(Request $request, SubIdxBatch $subIdxBatch)
    {
        if ($subIdxBatch->started_at) {
            abort(422, 'This batch has already started');
        }

        $user = user();

        $amountOfFiles = $subIdxBatch->files->count();

        if ($amountOfFiles > $user->batch_tokens_left) {
            abort(422);
        }

        $availableLanguages = array_map(function ($array) {
            return $array[0];
        }, $subIdxBatch->batchFileLanguageCount());

        $request->validate([
            'languages' => 'required|array|min:1',
            'languages.*' => 'required|distinct|in:'.implode(',', $availableLanguages),
        ]);

        $subIdxBatch->update(['started_at' => now()]);

        $user->update([
            'batch_tokens_left' => $user->batch_tokens_left - $amountOfFiles,
            'batch_tokens_used' => $user->batch_tokens_used + $amountOfFiles,
        ]);

        StartSubIdxBatchJob::dispatch($subIdxBatch, $request->get('languages'));

        return redirect()->route('user.subIdxBatch.show', $subIdxBatch);
    }
}
