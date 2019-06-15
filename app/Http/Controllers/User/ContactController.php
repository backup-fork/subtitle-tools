<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

class ContactController
{
    public function index()
    {
        return view('user.contact', [
            'user' => user(),
        ]);
    }

    public function post(Request $request)
    {
        //
    }
}
