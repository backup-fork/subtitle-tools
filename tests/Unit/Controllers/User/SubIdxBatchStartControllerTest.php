<?php

namespace Tests\Unit\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubIdxBatchStartControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var SubIdxBatch $subIdxBatch */
    private $subIdxBatch;

    public function settingUp()
    {
        $this->subIdxBatch = $this->createSubIdxBatch();
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

    }

    private function showStart($subIdxBatch)
    {
        return $this->get(route('user.subIdxBatch.showStart', $subIdxBatch));
    }

    private function postStart($subIdxBatch, array $languages)
    {
        return $this->post(route('user.subIdxBatch.start', $subIdxBatch), ['languages' => $languages]);
    }
}
