<?php

namespace Tests\Unit\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_user_contact_page()
    {
        $this->userLogin()
            ->showPage()
            ->assertStatus(200);
    }

    private function showPage()
    {
        return $this->get(route('user.contact.index'));
    }

    private function postContact($data)
    {
        return $this->post(route('user.contact.post'), $data);
    }
}
