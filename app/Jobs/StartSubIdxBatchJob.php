<?php

namespace App\Jobs;

use App\Models\SubIdx;
use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use App\Models\SubIdxLanguage;
use Illuminate\Contracts\Queue\ShouldQueue;

class StartSubIdxBatchJob extends BaseJob implements ShouldQueue
{
    public $timeout = 300;

    public $queue = 'default';

    public $subIdxBatch;

    public $extractLanguages;

    public function __construct(SubIdxBatch $subIdxBatch, array $extractLanguages)
    {
        $this->subIdxBatch = $subIdxBatch;

        $this->extractLanguages = $extractLanguages;
    }

    public function handle()
    {
        $this->subIdxBatch->files
            ->each(function (SubIdxBatchFile $batchFile) {
                $subIdx = SubIdx::createFromBatchFile($batchFile, $this->extractLanguages);

                $subIdx->languages()->update(['queued_at' => now()]);

                $subIdx->languages->each(function (SubIdxLanguage $subIdxLanguage) {
                    ExtractSubIdxLanguageJob::dispatch($subIdxLanguage)->onQueue('A150');
                });
            });

        $this->subIdxBatch->files()->delete();

        $this->subIdxBatch->unlinkedFiles()->delete();
    }
}
