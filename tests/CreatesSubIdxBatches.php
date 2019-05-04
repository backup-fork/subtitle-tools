<?php

namespace Tests;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use App\Models\SubIdxBatch\SubIdxUnlinkedBatchFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait CreatesSubIdxBatches
{
    public function createSubIdxBatch($user = null, $attributes = []): SubIdxBatch
    {
        if (is_array($user)) {
            [$user, $attributes] = [$attributes, $user];
        }

        $user = $user ?: $this->createUser();

        return factory(SubIdxBatch::class)->create($attributes + [
            'user_id' => $user->id,
        ]);
    }

    public function createUnlinkedBatchFile($subIdxBatch = null, $isSub = null): SubIdxUnlinkedBatchFile
    {
        $isSub = is_null($isSub) ? ((bool) mt_rand(0, 1)) : $isSub;

        $subIdxBatch = $subIdxBatch ?: $this->createSubIdxBatch();

        $type = $isSub ? 'sub' : 'idx';

        /** @var SubIdxUnlinkedBatchFile $unlinkedFile */
        $unlinkedFile = factory(SubIdxUnlinkedBatchFile::class)->state($type)->create([
            'id' => $uuid = Str::uuid()->toString(),
            'storage_file_path' => "sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid.$type",
            'sub_idx_batch_id' => $subIdxBatch->id,
        ]);

        Storage::put($unlinkedFile->storage_file_path, '');

        return $unlinkedFile;
    }

    public function createUnlinkedBatchFile_sub($subIdxBatch = null): SubIdxUnlinkedBatchFile
    {
        return $this->createUnlinkedBatchFile($subIdxBatch, true);
    }

    public function createUnlinkedBatchFile_idx($subIdxBatch = null): SubIdxUnlinkedBatchFile
    {
        return $this->createUnlinkedBatchFile($subIdxBatch, false);
    }

    public function createUnlinkedBatchFiles($count, $subIdxBatch = null)
    {
        static $toggle = 0;

        $unlinkedSubFiles = [];

        for ($i = 0; $i < $count; $i++) {
            $unlinkedSubFiles[] = $this->createUnlinkedBatchFile($subIdxBatch, (bool) ($toggle++ % 2));
        }

        return $unlinkedSubFiles;
    }

    public function createSubIdxBatchFile($subIdxBatch = null): SubIdxBatchFile
    {
        $subIdxBatch = $subIdxBatch ?: $this->createSubIdxBatch();

        /** @var SubIdxBatchFile $batchFile */
        $batchFile = factory(SubIdxBatchFile::class)->create([
            'id' => $uuid = Str::uuid()->toString(),
            'sub_idx_batch_id' => $subIdxBatch->id,
            'sub_storage_file_path' => "sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid/a.sub",
            'idx_storage_file_path' => "sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid/a.idx",
        ]);

        Storage::put($batchFile->sub_storage_file_path, '');
        Storage::put($batchFile->idx_storage_file_path, '');

        return $batchFile;
    }

    public function createSubIdxBatchFiles($count, $subIdxBatch = null)
    {
        $subIdxBatchFiles = [];

        for ($i = 0; $i < $count; $i++) {
            $subIdxBatchFiles[] = $this->createSubIdxBatchFile($subIdxBatch);
        }

        return $subIdxBatchFiles;
    }
}
