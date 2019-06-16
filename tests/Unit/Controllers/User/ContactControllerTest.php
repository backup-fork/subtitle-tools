<?php

namespace Tests\Unit\Controllers\User;

use App\Models\ContactForm;
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

    /** @test */
    function it_can_submit_a_contact_form()
    {
        $user = $this->createUser();

        $this->userLogin($user)
            ->postContact([
                'message' => 'hallo!',
            ])
            ->assertStatus(200)
            ->assertViewHas('messageSent', true)
            ->assertSeeText('Thank you for your message');

        $contactForm = ContactForm::firstOrFail();

        $this->assertSame($user->id, $contactForm->user_id);
        $this->assertNull($contactForm->email);
        $this->assertSame('hallo!', $contactForm->message);
        $this->assertNull($contactForm->replied_at);
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
