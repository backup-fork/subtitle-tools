<?php

namespace App\Events;

use App\Models\SubIdxBatch\SubIdxBatch;

class SubIdxBatchFinished extends BaseEvent
{
    public $subIdxBatch;

    public function __construct(SubIdxBatch $subIdxBatch)
    {
        $this->subIdxBatch = $subIdxBatch;
    }
}
