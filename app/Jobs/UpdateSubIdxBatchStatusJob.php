<?php

namespace App\Jobs;

use App\Events\SubIdxBatchFinished;
use App\Models\SubIdx;
use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxLanguage;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateSubIdxBatchStatusJob extends BaseJob implements ShouldQueue
{
    public $queue = 'default';

    public $subIdxBatch;

    public function __construct(SubIdxBatch $subIdxBatch)
    {
        $this->subIdxBatch = $subIdxBatch;
    }

    public function handle()
    {
        if ($this->subIdxBatch->finished_at) {
            return;
        }

        $allLanguagesFinished = SubIdx::query()
            ->with('languages')
            ->where('sub_idx_batch_id', $this->subIdxBatch->id)
            ->get()
            ->every(function (SubIdx $subIdx) {
                return $subIdx->languages->every(function (SubIdxLanguage $language) {
                    return $language->finished_at;
                });
            });

        if ($allLanguagesFinished) {
            $this->subIdxBatch->update(['finished_at' => now()]);

            SubIdxBatchFinished::dispatch($this->subIdxBatch);
        }
    }
}
