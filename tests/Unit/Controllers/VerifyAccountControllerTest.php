<?php

namespace Tests\Unit\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_verify_an_account()
    {
        $user = $this->createUser([
            'email_verification_token' => $token = sha1('token'),
            'email_verified_at' => null,
        ]);

        $this->showVerifyPage($user->email, $token)
            ->assertRedirect(route('user.dashboard.index'))
            ->assertStatus(302);

        $user->refresh();

        $this->assertAuthenticatedAs($user);

        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verification_token);
    }

    /** @test */
    function the_token_has_to_match_the_email()
    {
        $user = $this->createUser([
            'email_verification_token' => sha1('token'),
            'email_verified_at' => null,
        ]);

        $anotherUser = $this->createUser([
            'email_verification_token' => $anotherToken = sha1('token-2'),
            'email_verified_at' => null,
        ]);

        $this->showVerifyPage($user->email, $anotherToken)
            ->assertStatus(404);
    }

    /** @test */
    function the_email_has_to_match_the_token()
    {
        $user = $this->createUser([
            'email_verification_token' => $token = sha1('token'),
            'email_verified_at' => null,
        ]);

        $anotherUser = $this->createUser([
            'email_verification_token' => sha1('token-2'),
            'email_verified_at' => null,
        ]);

        $this->showVerifyPage($anotherUser->email, $token)
            ->assertStatus(404);
    }

    private function showVerifyPage($email, $token)
    {
        return $this->get(route('verifyEmail', [$email, $token]));
    }
}
