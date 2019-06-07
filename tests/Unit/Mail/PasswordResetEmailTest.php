<?php

namespace Tests\Unit;

use App\Mail\PasswordResetEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $snapshotDirectory = 'mail';

    /** @test */
    function it_can_render_the_email()
    {
        $user = $this->createUser(['email' => 'user@example.com']);

        $token = sha1('token');

        $email = new PasswordResetEmail($user->email, $token);

        $email->build();

        $this->assertTrue(
            $email->hasTo($user->email)
        );

        $this->assertMatchesEmailSnapshot($email);
    }
}
