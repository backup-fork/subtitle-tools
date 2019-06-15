<?php

namespace Tests\Unit\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyBatchTokensControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_buy_page()
    {
        $this->userLogin()
            ->showPage()
            ->assertStatus(200);
    }

    private function showPage()
    {
        return $this->get(route('user.account.buyBatchTokens.index'));
    }
}
