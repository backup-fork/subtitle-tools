<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ExtractSubIdxLanguageJob;
use App\Jobs\StartSubIdxBatchJob;
use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxLanguage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StartSubIdxBatchJobTest extends TestCase
{
    use RefreshDatabase;

    /** @var SubIdxBatch $subIdxBatch */
    private $subIdxBatch;

    public function settingUp()
    {
        $this->subIdxBatch = $this->createSubIdxBatch();
    }

    /** @test */
    function it_fires_extract_jobs_on_a_high_priority_queue()
    {
        Queue::fake();

        $batchFile = $this->createSubIdxBatchFile($this->subIdxBatch);

        $this->copyRealFileToStorage('sub-idx/many.sub', $batchFile->sub_storage_file_path);
        $this->copyRealFileToStorage('sub-idx/many.idx', $batchFile->idx_storage_file_path);

        $job = new StartSubIdxBatchJob($this->subIdxBatch, ['en', 'pl']);

        $job->handle();

        /** @var Collection $languages */
        $languages = $this->subIdxBatch->subIdxes->first()->languages;

        $this->assertCount(2, $languages);

        Queue::assertPushedOn('A150', ExtractSubIdxLanguageJob::class, function (ExtractSubIdxLanguageJob $job) use ($languages) {
            return $job->subIdxLanguage->id === $languages->first()->id;
        });

        Queue::assertPushedOn('A150', ExtractSubIdxLanguageJob::class, function (ExtractSubIdxLanguageJob $job) use ($languages) {
            return $job->subIdxLanguage->id === $languages->last()->id;
        });
    }

    /** @test */
    function it_extracts_all_the_languages_in_the_file()
    {
        Queue::fake();

        $batchFile = $this->createSubIdxBatchFile($this->subIdxBatch);

        $this->copyRealFileToStorage('sub-idx/en-en-en-es.sub', $batchFile->sub_storage_file_path);
        $this->copyRealFileToStorage('sub-idx/en-en-en-es.idx', $batchFile->idx_storage_file_path);

        $job = new StartSubIdxBatchJob($this->subIdxBatch, ['en', 'es']);

        $job->handle();

        /** @var Collection $languages */
        $languages = $this->subIdxBatch->subIdxes->first()->languages;

        $this->assertSame(
            ['0', '1', '2', '3'],
            $languages->pluck('index')->all()
        );

        $this->assertCount(4, $languages);

        Queue::assertPushed(ExtractSubIdxLanguageJob::class, 4);
    }
}
