<?php

namespace App\Mail;

class PasswordResetEmail extends BaseEmail
{
    public $email;

    public $token;

    public function __construct($email, $token)
    {
        $this->email = $email;

        $this->token = $token;
    }

    public function build()
    {
        return $this->to($this->email)
            ->subject('Password reset | Subtitle Tools')
            ->view('mail.password-reset');
    }
}
