<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowErrorLogControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_error_log()
    {
        $this->adminLogin()
            ->get(route('admin.showErrorLog'))
            ->assertStatus(200);
    }
}
