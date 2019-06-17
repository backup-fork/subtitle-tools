<?php

namespace Tests\Unit\Controllers\Admin;

use App\Models\ContactForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_mark_feedback_as_read()
    {
        $contactForm = factory(ContactForm::class)->create(['read_at' => null]);

        $this->adminLogin()
            ->post(route('admin.feedback.markAsRead', $contactForm->id))
            ->assertStatus(302);

        $this->assertNotNull($contactForm->refresh()->read_at);
    }
}
