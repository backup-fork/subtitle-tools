<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController
{
    use AuthenticatesUsers;

    public function index()
    {
        return view('login');
    }

    protected function authenticated(Request $request, $user)
    {
        return $user->is_admin
            ? redirect()->route('admin.dashboard.index')
            : redirect()->route('user.dashboard.index');
    }
}
