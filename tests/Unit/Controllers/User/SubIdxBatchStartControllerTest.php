<?php

namespace Tests\Unit\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Support\Facades\VobSub2Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SubIdxBatchStartControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var SubIdxBatch $subIdxBatch */
    private $subIdxBatch;

    public function settingUp()
    {
        $this->subIdxBatch = $this->createSubIdxBatch();

        VobSub2Srt::fake();
    }

    /** @test */
    function it_can_show_a_batch()
    {
        $this->createSubIdxBatchFiles(3, $this->subIdxBatch);

        $this->actingAs($this->subIdxBatch->user)
            ->showStart($this->subIdxBatch)
            ->assertStatus(200);
    }

    /** @test */
    function it_can_show_an_empty_batch()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->showStart($this->subIdxBatch)
            ->assertStatus(200)
            ->assertSee('to this batch yet');
    }

    /** @test */
    function it_will_only_show_your_own_batches()
    {
        $anotherUser = $this->createUser();

        $this->actingAs($anotherUser)
            ->showStart($this->subIdxBatch)
            ->assertStatus(403);
    }

    /** @test */
    function it_can_start_a_batch()
    {
//        $this->createSubIdxBatchFiles(3, $this->subIdxBatch);
//
//        $this->actingAs($this->subIdxBatch->user)
//            ->postStart($this->subIdxBatch, ['en', 'nl'])
//            ->assertSessionHasNoErrors()
//            ->assertStatus(302);
    }

    /** @test */
    function it_will_only_start_your_own_batches()
    {
        $anotherUser = $this->createUser();

        $this->actingAs($anotherUser)
            ->postStart($this->subIdxBatch, [])
            ->assertStatus(403);
    }

    /** @test */
    function you_have_to_post_at_least_one_language()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->postStart($this->subIdxBatch, [])
            ->assertSessionHasErrors()
            ->assertStatus(302);
    }

    /** @test */
    function you_cant_post_duplicate_languages()
    {
        $this->createSubIdxBatchFiles(3, $this->subIdxBatch);

        $this->setBatchLanguages($this->subIdxBatch, ['en', 'nl', 'es']);

        $this->actingAs($this->subIdxBatch->user)
            ->postStart($this->subIdxBatch, ['en', 'nl', 'en'])
            ->assertSessionHasErrors()
            ->assertStatus(302);
    }

    /** @test */
    function you_can_only_posts_available_languages()
    {
        $this->createSubIdxBatchFiles(3, $this->subIdxBatch);

        $this->setBatchLanguages($this->subIdxBatch, ['en', 'nl', 'es']);

        $this->actingAs($this->subIdxBatch->user)
            ->postStart($this->subIdxBatch, ['en', 'nl', 'en'])
            ->assertSessionHasErrors()
            ->assertStatus(302);
    }

    private function showStart($subIdxBatch)
    {
        return $this->get(route('user.subIdxBatch.showStart', $subIdxBatch));
    }

    private function postStart($subIdxBatch, array $languages)
    {
        return $this->post(route('user.subIdxBatch.start', $subIdxBatch), ['languages' => $languages]);
    }

    private function setBatchLanguages(SubIdxBatch $subIdxBatch, $languages = [])
    {
        $batchFile = $subIdxBatch->files->first();

        if (! $batchFile) {
            $this->fail('called "setBatchLanguages()" on a batch with no files');
        }

        Cache::rememberForever($batchFile->id, function () use ($languages) {
            return $languages;
        });
    }
}
