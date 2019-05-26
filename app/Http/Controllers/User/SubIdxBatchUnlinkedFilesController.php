<?php

namespace App\Http\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxUnlinkedBatchFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SubIdxBatchUnlinkedFilesController
{
    public function index(SubIdxBatch $subIdxBatch)
    {
        if ($subIdxBatch->started_at) {
            return redirect()->route('user.subIdxBatch.show', $subIdxBatch);
        }

        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-unlinked', [
            'subIdxBatch' => $subIdxBatch,
            'unlinkedSubFiles' => $subIdxBatch->unlinkedFiles->where('is_sub', true)->all(),
            'unlinkedIdxFiles' => $subIdxBatch->unlinkedFiles->where('is_sub', false)->all(),
        ]);
    }

    public function link(Request $request, SubIdxBatch $subIdxBatch)
    {
        if ($subIdxBatch->started_at) {
            abort(422, 'This batch has already started');
        }

        [$unlinkedSub, $unlinkedIdx] = $this->validate($request, $subIdxBatch);

        $uuid = Str::uuid()->toString();

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

        $alreadyExists = $subIdxBatch->files()
            ->where('sub_hash', $sub->hash)
            ->where('idx_hash', $idx->hash)
            ->exists();

        if ($alreadyExists) {
            throw ValidationException::withMessages(['alreadyLinked' => '1']);
        }

        return [$sub, $idx];
    }
}
