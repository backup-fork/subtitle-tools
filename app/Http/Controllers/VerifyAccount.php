<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyAccount
{
    public function __invoke($email, $token)
    {
        $user = User::query()
            ->where('email', $email)
            ->where('email_verification_token', $token)
            ->whereNull('email_verified_at')
            ->firstOrFail();

        $user->update([
            'email_verification_token' => null,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('user.dashboard.index');
    }
}
