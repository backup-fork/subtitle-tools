<?php

namespace App\Http\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxUnlinkedBatchFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubIdxBatchUnlinkedFilesController
{
    public function index(SubIdxBatch $subIdxBatch)
    {
        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-unlinked', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }

    public function link(Request $request, SubIdxBatch $subIdxBatch)
    {
        [$unlinkedSub, $unlinkedIdx] = $this->validate($request, $subIdxBatch);

        $uuid = Str::uuid();

        Storage::move($unlinkedSub->storage_file_path, $subStoragePath = "sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid/a.sub");
        Storage::move($unlinkedIdx->storage_file_path, $idxStoragePath = "sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid/a.idx");

        $subIdxBatch->files()->create([
            'id' => $uuid,
            'sub_original_name' => $unlinkedSub->original_name,
            'idx_original_name' => $unlinkedIdx->original_name,
            'sub_hash' => $unlinkedSub->hash,
            'idx_hash' => $unlinkedIdx->hash,
            'sub_storage_file_path' => $subStoragePath,
            'idx_storage_file_path' => $idxStoragePath,
        ]);

        $unlinkedSub->delete();
        $unlinkedIdx->delete();

        return back();
    }

    /** @return SubIdxUnlinkedBatchFile[] */
    private function validate(Request $request, SubIdxBatch $subIdxBatch)
    {
        $request->validate([
            'sub' => 'required|string|uuid',
            'idx' => 'required|string|uuid',
        ]);

        $sub = $subIdxBatch->unlinkedFiles
            ->where('id', $request->get('sub'))
            ->where('is_sub', true)
            ->first();

        $idx = $subIdxBatch->unlinkedFiles
            ->where('id', $request->get('idx'))
            ->where('is_sub', false)
            ->first();

        if (! $sub || ! $idx) {
            abort(422);
        }

        return [$sub, $idx];
    }
}
