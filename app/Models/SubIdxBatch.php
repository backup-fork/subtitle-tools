<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubIdxBatch extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'max_files' => 'int',
    ];
}
