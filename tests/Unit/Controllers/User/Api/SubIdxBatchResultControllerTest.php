<?php

namespace Tests\Unit\Controllers\User\Api;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubIdxBatchResultControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var SubIdxBatch $subIdxBatch */
    private $subIdxBatch;

    public function settingUp()
    {
        $this->subIdxBatch = $this->createSubIdxBatch(['started_at' => now()]);
    }

    /** @test */
    function you_can_only_see_your_own_batch()
    {
        $anotherUser = $this->createUser();

        $this->apiUserLogin($anotherUser)
            ->getBatchResult($this->subIdxBatch)
            ->assertStatus(403);
    }

    /** @test */
    function it_shows_the_batch_sub_idx_files()
    {
        $this->apiUserLogin($this->subIdxBatch->user)
            ->getBatchResult($this->subIdxBatch)
            ->assertStatus(200);
    }

    private function getBatchResult($subIdxBatch)
    {
        return $this->getJson(route('api.user.subIdxBatch.result', $subIdxBatch));
    }
}
