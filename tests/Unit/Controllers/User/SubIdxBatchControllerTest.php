<?php

namespace Tests\Unit\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    private function getBatchIndex()
    {
        return $this->get(route('user.subIdxBatch.index'));
    }

    private function showBatchCreate()
    {
        return $this->get(route('user.subIdxBatch.create'));
    }

    private function storeBatch($data)
    {
        return $this->post(route('user.subIdxBatch.store'), $data);
    }
}
