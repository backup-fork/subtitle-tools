<?php

namespace App\Listeners\SubIdxBatch;

use App\Events\SubIdxBatchFinished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class RemoveSourceFiles implements ShouldQueue
{
    public $queue = 'default';

    public function handle(SubIdxBatchFinished $event)
    {
        Storage::deleteDirectory("sub-idx-batches/{$event->subIdxBatch->user_id}/{$event->subIdxBatch->id}");
    }
}
