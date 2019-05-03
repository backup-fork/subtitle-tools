<?php

namespace Tests;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use App\Models\SubIdxBatch\SubIdxUnlinkedBatchFile;

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

    public function createUnlinkedBatchFile($subIdxBatch = null, $attributes = []): SubIdxUnlinkedBatchFile
    {
        if (is_array($subIdxBatch)) {
            [$subIdxBatch, $attributes] = [$attributes, $subIdxBatch];
        }

        $subIdxBatch = $subIdxBatch ?: $this->createSubIdxBatch();

        return factory(SubIdxUnlinkedBatchFile::class)->create([
            'sub_idx_batch_id' => $subIdxBatch->id,
        ] + $attributes);
    }


    public function createUnlinkedBatchFile_sub($subIdxBatch = null): SubIdxUnlinkedBatchFile
    {
        return $this->createUnlinkedBatchFile($subIdxBatch, ['is_sub' => true]);
    }

    public function createUnlinkedBatchFile_idx($subIdxBatch = null): SubIdxUnlinkedBatchFile
    {
        return $this->createUnlinkedBatchFile($subIdxBatch, ['is_sub' => false]);
    }

    public function createUnlinkedBatchFiles($count, $subIdxBatch = null)
    {
        static $toggle = 0;

        $unlinkedSubFiles = [];

        for ($i = 0; $i < $count; $i++) {
            $unlinkedSubFiles[] = $this->createUnlinkedBatchFile($subIdxBatch, ['is_sub' => (bool) ($toggle++ % 2)]);
        }

        return $unlinkedSubFiles;
    }

    public function createSubIdxBatchFile($subIdxBatch = null): SubIdxBatchFile
    {
        $subIdxBatch = $subIdxBatch ?: $this->createSubIdxBatch();

        return factory(SubIdxBatchFile::class)->create([
            'sub_idx_batch_id' => $subIdxBatch->id,
        ]);
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
