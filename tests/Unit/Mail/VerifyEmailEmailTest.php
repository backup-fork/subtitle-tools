<?php

namespace Tests\Mail;

use App\Mail\VerifyEmailEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyEmailEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $snapshotDirectory = 'mail';

    /** @test */
    function it_can_render_the_email()
    {
        $user = $this->createUser([
            'email' => 'user@example.com',
            'email_verification_token' => $token = sha1('token'),
            'email_verified_at' => null,
        ]);

        $email = new VerifyEmailEmail($user->email, $token);

        $email->build();

        $this->assertTrue(
            $email->hasTo($user->email)
        );

        $this->assertMatchesEmailSnapshot($email);
    }
}
