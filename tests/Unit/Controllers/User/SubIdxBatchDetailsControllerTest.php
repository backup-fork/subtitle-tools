<?php

namespace Tests\Unit\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubIdxBatchDetailsControllerTest extends TestCase
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

        $this->actingAs($anotherUser)
            ->showBatch($this->subIdxBatch)
            ->assertStatus(403);
    }

    /** @test */
    function it_redirects_if_the_batch_has_not_started_yet()
    {
        $subIdxBatch = $this->createSubIdxBatch(['started_at' => null]);

        $this->actingAs($subIdxBatch->user)
            ->showBatch($subIdxBatch)
            ->assertRedirect(route('user.subIdxBatch.showUpload', $subIdxBatch));
    }

    private function showBatch($subIdxBatch)
    {
        return $this->get(route('user.subIdxBatch.show', $subIdxBatch));
    }
}
