<?php

namespace App\Http\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class UniqueEmailRule implements Rule
{
    public function passes($attribute, $email)
    {
        $email = $this->normalizeEmail($email);

        $allEmails = User::pluck('email')->map(function ($email) {
            return $this->normalizeEmail($email);
        });

        return ! $allEmails->contains($email);
    }

    protected function normalizeEmail($email)
    {
        [$name, $host] = explode('@', strtolower($email), 2);

        // test+1@example.com
        $name = Str::before($name, '+');

        // te.st@example.com
        $name = str_replace('.', '', $name);

        return $name.'@'.$host;
    }

    public function message()
    {
        return 'It looks like an account already exists with this email';
    }
}
