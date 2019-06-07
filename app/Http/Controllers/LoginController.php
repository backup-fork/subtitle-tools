<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    use AuthenticatesUsers;

    public function index()
    {
        return view('guest.auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        if (! $user->email_verified_at) {
            Auth::logout();

            return back()->withErrors(['email' => 'You must verify your email first']);
        }

        return $user->is_admin
            ? redirect()->route('admin.dashboard.index')
            : redirect()->route('user.dashboard.index');
    }
}
