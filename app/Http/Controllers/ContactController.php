<?php

namespace App\Http\Controllers;

use App\Models\ContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactController
{
    public function index()
    {
        return view('contact');
    }

    public function post(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:10000',
            'email' => 'present|nullable|string|max:255',
            'captcha' => 'required|numeric|regex:/^5$/',
        ]);

        ContactForm::create([
            'id' => Str::uuid(),
            'email' => $request->get('email'),
            'ip' => $request->ip(),
            'message' => $request->get('message'),
        ]);

        return view('contact', ['sentMessage' => true]);
    }
}
