<?php

namespace App\Http\Controllers\User\Api;

use App\Http\Resources\SubIdxResource;
use App\Models\SubIdxBatch\SubIdxBatch;

class SubIdxBatchResult
{
    public function __invoke(SubIdxBatch $subIdxBatch)
    {
        $subIdxBatch->load('subIdxes', 'subIdxes.languages');

        return SubIdxResource::collection($subIdxBatch->subIdxes);
    }
}
