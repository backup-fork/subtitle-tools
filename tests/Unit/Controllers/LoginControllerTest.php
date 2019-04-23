<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_login_page()
    {
        $this->showLogin()->assertStatus(200);
    }

    /** @test */
    function it_can_login_an_admin()
    {
        $user = factory(User::class)->create(['is_admin' => true]);

        $this->assertGuest();

        $this->postLogin(['email' => $user->email, 'password' => 'password'])
            ->assertSessionDoesntHaveErrors()
            ->assertStatus(302)
            ->assertRedirect(route('admin.dashboard.index'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    function it_can_login_a_user()
    {
        $user = factory(User::class)->create(['is_admin' => false]);

        $this->assertGuest();

        $this->postLogin(['email' => $user->email, 'password' => 'password'])
            ->assertSessionDoesntHaveErrors()
            ->assertStatus(302)
            ->assertRedirect(route('user.dashboard.index'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    function it_can_fail_a_login()
    {
        $user = factory(User::class)->create();

        $this->assertGuest();

        $this->postLogin(['email' => $user->email, 'password' => 'wrong!!'])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->assertGuest();
    }

    /** @test */
    function it_can_logout()
    {
        $this->adminLogin()
            ->postLogout()
            ->assertStatus(302);

        $this->assertGuest();
    }

    private function showLogin()
    {
        return $this->get(route('login'));
    }

    private function postLogin($data)
    {
        return $this->post(route('login.post'), $data);
    }

    private function postLogout()
    {
        return $this->post(route('logout'));
    }
}
