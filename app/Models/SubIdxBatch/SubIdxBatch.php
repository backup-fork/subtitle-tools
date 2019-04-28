<?php

namespace App\Models\SubIdxBatch;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SubIdxBatch extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'max_files' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(SubIdxBatchFile::class);
    }

    public function unlinkedFiles()
    {
        return $this->hasMany(SubIdxUnlinkedBatchFile::class);
    }
}
