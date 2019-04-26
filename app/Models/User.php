<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = [];

    public $incrementing = false;

    protected $casts = [
        'is_admin' => 'bool',
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function subIdxBatches()
    {
        return $this->hasMany(SubIdxBatch::class);
    }
}
