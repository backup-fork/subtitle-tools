<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_reset_page()
    {
        $this->showResetPage('token')->assertStatus(200);
    }

    /** @test */
    function it_auto_fills_the_email_if_present()
    {
        $this->showResetPage('token', 'user@example.com')
            ->assertStatus(200)
            ->assertSee('value="user@example.com"');
    }

    /** @test */
    function it_can_reset_a_password()
    {
        $user = $this->createUser();

        [$email, $token] = $this->createToken($user);

        $this->postPasswordReset($token, [
                'email' => $email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('user.dashboard.index'));

        $this->assertAuthenticatedAs($user);

        $this->assertFalse(
            DB::table('password_resets')->where('email', $email)->exists()
        );

        $this->assertTrue(
            Hash::check('new-password', $user->refresh()->password)
        );
    }

    /** @test */
    function the_email_needs_to_match_the_token()
    {
        $user = $this->createUser();

        $anotherUser = $this->createUser();

        [$email, $token] = $this->createToken($user);

        $this->postPasswordReset($token, [
                'email' => $anotherUser->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasErrors()
            ->assertStatus(302);
    }

    /** @test */
    function the_token_needs_to_match_the_email()
    {
        $user = $this->createUser();

        [$email, $token] = $this->createToken($user);

        $this->postPasswordReset(sha1('another-token'), [
                'email' => $email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasErrors()
            ->assertStatus(302);
    }

    private function createToken($email)
    {
        if ($email instanceof User) {
            $email = $email->email;
        }

        $token = sha1(Str::random());

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);

        return [$email, $token];
    }

    private function showResetPage($token, $email = null)
    {
        if ($email) {
            $email = '?email='.$email;
        }

        return $this->get(route('resetPassword.index', $token).$email);
    }

    private function postPasswordReset($token, $data)
    {
        return $this->post(route('resetPassword.post', $token), $data);
    }
}
