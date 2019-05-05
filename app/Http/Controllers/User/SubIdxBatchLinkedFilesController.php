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
        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-linked', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }

    public function unlink(SubIdxBatchFile $subIdxBatchFile)
    {
        $batch = $subIdxBatchFile->subIdxBatch;

        $subUuid = Str::uuid()->toString();
        $idxUuid = Str::uuid()->toString();

        Storage::move($subIdxBatchFile->sub_storage_file_path, $subStoragePath = "sub-idx-batches/$batch->user_id/$batch->id/$subUuid/a.sub");
        Storage::move($subIdxBatchFile->idx_storage_file_path, $idxStoragePath = "sub-idx-batches/$batch->user_id/$batch->id/$idxUuid/a.idx");

        $batch->unlinkedFiles()->createMany([[
                'id' => $subUuid,
                'original_name' => $subIdxBatchFile->sub_original_name,
                'hash' => $subIdxBatchFile->sub_hash,
                'is_sub' => true,
                'storage_file_path' => $subStoragePath,
            ], [
                'id' => $idxUuid,
                'original_name' => $subIdxBatchFile->idx_original_name,
                'hash' => $subIdxBatchFile->idx_hash,
                'is_sub' => false,
                'storage_file_path' => $idxStoragePath,
        ]]);

        $subIdxBatchFile->delete();

        return back();
    }
}
