<?php

namespace App\Mail;

class VerifyEmailEmail extends BaseEmail
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
            ->subject('Verify your email | Subtitle Tools')
            ->view('mail.verify-email');
    }
}
