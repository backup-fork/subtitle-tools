<?php

namespace Tests\Unit\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubIdxBatchControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_will_only_show_your_own_batches()
    {
    }
}
