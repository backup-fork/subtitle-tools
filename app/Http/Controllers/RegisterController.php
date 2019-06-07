<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmailEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController
{
    public function index()
    {
        return view('guest.auth.register');
    }

    public function post(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'id' => Str::uuid()->toString(),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'email_verification_token' => $token = sha1(Str::random()),
        ]);

        Mail::queue(
            new VerifyEmailEmail($user->email, $token)
        );

        return redirect()->route('register.success');
    }

    public function success()
    {
        return view('guest.auth.register-success');
    }
}
