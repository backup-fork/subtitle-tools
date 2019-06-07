<?php

namespace Tests\Unit\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordUserActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_updates_the_last_seen_at()
    {
        $user = $this->createUser();

        $this->progressTimeInHours(1);

        $this->actingAs($user)
            ->get(route('user.dashboard.index'))
            ->assertStatus(200);

        $this->assertSame(
            (string) $user->updated_at,
            (string) $user->refresh()->updated_at
        );

        $this->assertNow($user->last_seen_at);
    }
}
