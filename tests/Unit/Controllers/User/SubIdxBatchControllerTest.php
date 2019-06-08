<?php

namespace Tests\Unit\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubIdxBatchControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_your_own_batches()
    {
        $user = $this->createUser();
        $b1 = $this->createSubIdxBatch($user);
        $b2 = $this->createSubIdxBatch($user);

        $anotherUser = $this->createUser();
        $notMyBatch = $this->createSubIdxBatch($anotherUser);

        $this->userLogin($user)
            ->getBatchIndex()
            ->assertStatus(200)
            ->assertSee($b1->id)
            ->assertSee($b2->id)
            ->assertDontSee($notMyBatch->id);
    }

    /** @test */
    function it_can_create_a_new_batch()
    {
        $user = $this->createUser();

        $this->userLogin($user)
            ->storeBatch()
            ->assertStatus(302);

        $this->assertCount(1, $user->subIdxBatches);
    }

    /** @test */
    function it_can_delete_a_batch()
    {
        $user = $this->createUser();

        $batch = $this->createSubIdxBatch($user);

        Storage::put("sub-idx-batches/$batch->user_id/$batch->id/abc/1.txt", 'bla bla bla');

        $this->userLogin($user)
            ->deleteBatch($batch)
            ->assertRedirect(route('user.subIdxBatch.index'));

        $this->assertModelDoesntExist($batch);

        Storage::assertMissing("sub-idx-batches/$batch->user_id/$batch->id");
        Storage::assertExists("sub-idx-batches/$batch->user_id");
    }

    /** @test */
    function you_can_only_delete_your_own_batches()
    {
        $batch = $this->createSubIdxBatch();

        $anotherUser = $this->createUser();

        $this->userLogin($anotherUser)
            ->deleteBatch($batch)
            ->assertStatus(403);
    }

    /** @test */
    function you_cant_delete_a_started_batch()
    {
        $user = $this->createUser();

        $batch = $this->createSubIdxBatch($user, ['started_at' => now()]);

        $this->userLogin($user)
            ->deleteBatch($batch)
            ->assertStatus(422);
    }

    private function getBatchIndex()
    {
        return $this->get(route('user.subIdxBatch.index'));
    }

    private function storeBatch()
    {
        return $this->post(route('user.subIdxBatch.store'));
    }

    private function deleteBatch($batch)
    {
        return $this->delete(route('user.subIdxBatch.delete', $batch));
    }
}
