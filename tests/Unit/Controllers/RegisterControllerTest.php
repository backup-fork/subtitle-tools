<?php

namespace Tests\Unit\Controllers;

use App\Mail\VerifyEmailEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function settingUp()
    {
        Mail::fake();
    }

    /** @test */
    function it_can_show_the_register_page()
    {
        $this->showRegisterPage()->assertStatus(200);
    }

    /** @test */
    function it_can_show_the_success_page()
    {
        $this->showSuccessPage()->assertStatus(200);
    }

    /** @test */
    function it_can_register_an_account()
    {
        $this->postRegister([
                'email' => 'user@example.com',
                'password' => 'the-password',
                'password_confirmation' => 'the-password',
            ])
            ->assertRedirect(route('register.success'));

        /** @var User $user */
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $this->assertNotNull($user->email_verification_token);
        $this->assertNull($user->email_verified_at);

        $this->assertTrue(
            Hash::check('the-password', $user->password)
        );

        Mail::assertQueued(VerifyEmailEmail::class, 1);

        Mail::assertQueued(VerifyEmailEmail::class, function (VerifyEmailEmail $email) use ($user) {
            return $user->email === $email->email && $email->token === $user->email_verification_token;
        });
    }

    private function showRegisterPage()
    {
        return $this->get(route('register.index'));
    }

    private function showSuccessPage()
    {
        return $this->get(route('register.success'));
    }

    private function postRegister($data)
    {
        return $this->post(route('register.post'), $data);
    }
}
