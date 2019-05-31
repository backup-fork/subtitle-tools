<?php

namespace App\Http\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubIdxBatchLinkedFilesController
{
    public function index(SubIdxBatch $subIdxBatch)
    {
        if ($subIdxBatch->started_at) {
            return redirect()->route('user.subIdxBatch.show', $subIdxBatch);
        }

        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-linked', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }

    public function unlink(SubIdxBatchFile $subIdxBatchFile)
    {
        $batch = $subIdxBatchFile->subIdxBatch;

        if ($batch->started_at) {
            abort(422, 'This batch has already started');
        }


        $subAlreadyExistsAsUnlinked = $batch->unlinkedFiles()
            ->where('hash', $subIdxBatchFile->sub_hash)
            ->exists();

        if (! $subAlreadyExistsAsUnlinked) {
            $subUuid = Str::uuid()->toString();

            Storage::move($subIdxBatchFile->sub_storage_file_path, $subStoragePath = "sub-idx-batches/$batch->user_id/$batch->id/$subUuid.sub");

            $batch->unlinkedFiles()->create([
                'id' => $subUuid,
                'original_name' => $subIdxBatchFile->sub_original_name,
                'hash' => $subIdxBatchFile->sub_hash,
                'is_sub' => true,
                'storage_file_path' => $subStoragePath,
            ]);
        } else {
            Storage::delete($subIdxBatchFile->sub_storage_file_path);
        }

        $idxAlreadyExistsAsUnlinked = $batch->unlinkedFiles()
            ->where('hash', $subIdxBatchFile->idx_hash)
            ->exists();

        if (! $idxAlreadyExistsAsUnlinked) {
            $idxUuid = Str::uuid()->toString();

            Storage::move($subIdxBatchFile->idx_storage_file_path, $idxStoragePath = "sub-idx-batches/$batch->user_id/$batch->id/$idxUuid.idx");

            $batch->unlinkedFiles()->create([
                'id' => $idxUuid,
                'original_name' => $subIdxBatchFile->idx_original_name,
                'hash' => $subIdxBatchFile->idx_hash,
                'is_sub' => false,
                'storage_file_path' => $idxStoragePath,
            ]);
        } else {
            Storage::delete($subIdxBatchFile->idx_storage_file_path);
        }

        $subIdxBatchFile->delete();

        return view('user.sub-idx-batch.show-linked', [
            'subIdxBatch' => $batch,
            'subAlreadyExistsAsUnlinked' => $subAlreadyExistsAsUnlinked,
            'idxAlreadyExistsAsUnlinked' => $idxAlreadyExistsAsUnlinked,
        ]);
    }
}
