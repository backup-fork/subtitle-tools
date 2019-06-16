<?php

namespace Tests\Unit\Controllers;

use App\Models\ContactForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_sends_feedback()
    {
        $this->post(route('contact.post'), [
                'message' => 'Message Text',
                'email' => 'Email Text',
                'captcha' => '5',
            ])
            ->assertStatus(200)
            ->assertSessionHasNoErrors()
            ->assertSee('Thank you for your message');

        $contactForm = ContactForm::firstOrFail();

        $this->assertSame('Email Text', $contactForm->email);
        $this->assertSame('Message Text', $contactForm->message);
        $this->assertNull($contactForm->user_id);
    }

    /** @test */
    function it_sends_feedback_without_an_email()
    {
        $this->post(route('contact.post'), [
                'message' => 'Message Text',
                'email' => null,
                'captcha' => '5',
            ])
            ->assertStatus(200)
            ->assertSessionHasNoErrors()
            ->assertSee('Thank you for your message');

        $contactForm = ContactForm::firstOrFail();

        $this->assertNull($contactForm->email);
        $this->assertSame('Message Text', $contactForm->message);
        $this->assertNull($contactForm->user_id);
    }

    /** @test */
    function message_is_required()
    {
        $this->post(route('contact.post'), [
                'email' => 'Email Text',
                'captcha' => '5',
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors('message');
    }

    /** @test */
    function captcha_must_be_correct()
    {
        $this->post(route('contact.post'), [
                'message' => 'content',
                'captcha' => '6',
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors('captcha');
    }

    /** @test */
    function it_has_the_correct_input_names()
    {
        $this->get(route('contact'))
            ->assertSee('name="message"')
            ->assertSee('name="email"')
            ->assertSee('name="captcha"');
    }
}
