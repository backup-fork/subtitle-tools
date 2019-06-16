<?php

namespace App\Http\Controllers\User;

use App\Models\ContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $request->validate([
            'message' => 'required|string|max:10000',
        ]);

        ContactForm::create([
            'id' => Str::uuid(),
            'user_id' => $request->user()->id,
            'ip' => $request->ip(),
            'message' => $request->get('message'),
        ]);

        return view('user.contact', [
            'user' => user(),
            'messageSent' => true,
        ]);
    }
}
