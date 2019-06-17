<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactForm;

class FeedbackController
{
    public function markAsRead(ContactForm $contactForm)
    {
        $contactForm->update(['read_at' => now()]);

        return back();
    }
}
