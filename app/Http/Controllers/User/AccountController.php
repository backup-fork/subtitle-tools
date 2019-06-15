<?php

namespace App\Http\Controllers\User;

class AccountController
{
    public function index()
    {
        return view('user.account.index', [
            'user' => user(),
        ]);
    }
}
