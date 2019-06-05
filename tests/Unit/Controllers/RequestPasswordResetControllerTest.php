<?php

namespace Tests\Unit\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestPasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_request_page()
    {
        $this->showRequestPage()->assertStatus(200);
    }

    /** @test */
    function it_can_show_the_success_page()
    {
        $this->showSuccessPage()->assertStatus(200);
    }

    /** @test */
    function it_can_request_a_reset()
    {
        $this->fail('todo');
    }

    /** @test */
    function it_shows_if_the_email_does_not_exist()
    {
        $this->fail('todo');
    }

    private function showRequestPage()
    {
        return $this->get(route('requestPasswordReset.index'));
    }

    private function postRequestReset($email)
    {
        return $this->post(route('requestPasswordReset.post'), ['data' => $email]);
    }

    private function showSuccessPage()
    {
        return $this->get(route('requestPasswordReset.success'));
    }
}
