<?php

namespace Tests\Unit\Jobs;

use App\Events\SubIdxBatchFinished;
use App\Jobs\UpdateSubIdxBatchStatusJob;
use App\Models\SubIdxLanguage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateSubIdxBatchStatusJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_fires_an_event_when_all_languages_are_finished()
    {
        Event::fake();

        $batch = $this->createSubIdxBatch();

        $subIdx1 = $this->createSubIdx(['sub_idx_batch_id' => $batch->id]);
        $subIdx1->languages()->saveMany([
            factory(SubIdxLanguage::class)->state('finished')->make(),
            factory(SubIdxLanguage::class)->state('finished')->make(),
        ]);

        $subIdx2 = $this->createSubIdx(['sub_idx_batch_id' => $batch->id]);
        $subIdx2->languages()->saveMany([
            factory(SubIdxLanguage::class)->state('finished')->make(),
        ]);

        (new UpdateSubIdxBatchStatusJob($batch))->handle();

        $this->assertNotNull($batch->refresh()->finished_at);

        Event::assertDispatchedTimes(SubIdxBatchFinished::class, 1);
    }

    /** @test */
    function it_doesnt_fire_an_event_when_all_languages_are_not_finished()
    {
        Event::fake();

        $batch = $this->createSubIdxBatch();

        $subIdx1 = $this->createSubIdx(['sub_idx_batch_id' => $batch->id]);
        $subIdx1->languages()->saveMany([
            factory(SubIdxLanguage::class)->state('finished')->make(),
            factory(SubIdxLanguage::class)->state('processing')->make(),
        ]);

        $subIdx2 = $this->createSubIdx(['sub_idx_batch_id' => $batch->id]);
        $subIdx2->languages()->saveMany([
            factory(SubIdxLanguage::class)->state('finished')->make(),
        ]);

        (new UpdateSubIdxBatchStatusJob($batch))->handle();

        $this->assertNull($batch->refresh()->finished_at);

        Event::assertNotDispatched(SubIdxBatchFinished::class);
    }
}
