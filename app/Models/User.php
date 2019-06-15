<?php

namespace App\Models;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $guarded = [];

    public $incrementing = false;

    protected $casts = [
        'is_admin' => 'bool',
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'batch_tokens_left' => 'int',
        'batch_tokens_used' => 'int',
    ];

    public function subIdxBatches()
    {
        return $this->hasMany(SubIdxBatch::class);
    }
}
