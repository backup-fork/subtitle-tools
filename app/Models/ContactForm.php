<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    protected $casts = [
        'replied_at' => 'datetime',
    ];
}
