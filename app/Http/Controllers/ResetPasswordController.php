<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResetPasswordController
{
    public function index(Request $request)
    {
        return view('guest.auth.reset-password', [
            'email' => $request->get('email'),
        ]);
    }

    public function post(Request $request, $token)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = $request->get('email');

        $hasToken = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->delete();

        if (! $hasToken) {
            return back()->withErrors(['email' => 'No reset request found for this email']);
        }

        $user = User::where('email', $email)->firstOrFail();

        $user->update([
            'password' => bcrypt($request->get('password')),
        ]);

        Auth::login($user);

        return redirect()->route('user.dashboard.index');
    }
}
