<?php

use App\Models\ContactForm;
use Illuminate\Database\Seeder;

class ContactFormTableSeeder extends Seeder
{
    public function run()
    {
        factory(ContactForm::class, 2)->create();
    }
}
