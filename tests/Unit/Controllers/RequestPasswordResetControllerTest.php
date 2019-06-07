<?php

namespace Tests\Unit\Controllers;

use App\Mail\PasswordResetEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class RequestPasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function settingUp()
    {
        Mail::fake();
    }

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
        $user = $this->createUser();

        $this->postRequestReset($user->email)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('requestPasswordReset.success'));

        $record = DB::table('password_resets')->where('email', $user->email)->first();

        $this->assertNotNull($record->token);

        Mail::assertQueued(PasswordResetEmail::class, 1);

        Mail::assertQueued(PasswordResetEmail::class, function (PasswordResetEmail $email) use ($user) {
            return $email->email === $user->email;
        });
    }

    /** @test */
    function it_throttles_reset_requests()
    {
        $user = $this->createUser();

        $this->createToken($user, now()->subMinutes(5));

        $this->postRequestReset($user->email)
            ->assertSessionHasErrors(['email' => 'You already requested a password reset recently'])
            ->assertStatus(302);

        Mail::assertNothingQueued();
    }

    /** @test */
    function it_updates_existing_tokens()
    {
        $user = $this->createUser();

        [$email, $token] = $this->createToken($user, now()->subHours(2));

        $this->postRequestReset($user->email)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('requestPasswordReset.success'));

        $record = DB::table('password_resets')->where('email', $user->email)->first();

        $this->assertNotSame($record->token, $token);
    }

    /** @test */
    function it_validates_the_email()
    {
        $user = $this->createUser();

        $this->postRequestReset('wrong@example.com')
            ->assertSessionHasErrors('email')
            ->assertStatus(302);
    }

    private function createToken($email, $createdAt = null)
    {
        if ($email instanceof User) {
            $email = $email->email;
        }

        $token = sha1(Str::random());

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => $createdAt ?? now(),
        ]);

        return [$email, $token];
    }

    private function showRequestPage()
    {
        return $this->get(route('requestPasswordReset.index'));
    }

    private function postRequestReset($email)
    {
        return $this->post(route('requestPasswordReset.post'), ['email' => $email]);
    }

    private function showSuccessPage()
    {
        return $this->get(route('requestPasswordReset.success'));
    }
}
