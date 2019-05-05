<?php

namespace App\Models\SubIdxBatch;

use Illuminate\Database\Eloquent\Model;

class SubIdxUnlinkedBatchFile extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'is_sub' => 'bool',
    ];

    public function subIdxBatch()
    {
        return $this->belongsTo(SubIdxBatch::class);
    }
}
