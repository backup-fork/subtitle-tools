<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RequestPasswordResetController
{
    public function index()
    {
        return view('guest.auth.request-password-reset');
    }

    public function post(Request $request)
    {
        $email = $request->get('email');

        $exists = User::where('email', $email)->exists();

        if (! $exists) {
            return back()->withErrors(['email' => 'No user found with this email']);
        }

        $hasRecentlyRequested = DB::table('password_resets')
            ->where('email', $email)
            ->where('created_at', '>', now()->subMinutes(30)->toDateTimeString())
            ->exists();

        if ($hasRecentlyRequested) {
            return back()->withErrors(['email' => 'You already requested a password reset recently']);
        }

        DB::table('password_resets')->updateOrInsert([
            'email' => $email,
        ], [
            'token' => $token = sha1(Str::random()),
            'created_at' => now(),
        ]);

        Mail::queue(
            new PasswordResetEmail($email, $token)
        );

        return redirect()->route('requestPasswordReset.success');
    }

    public function success()
    {
        return view('guest.auth.request-password-reset-success');
    }
}
