<?php

namespace App\Models\SubIdxBatch;

use Illuminate\Database\Eloquent\Model;

class SubIdxBatchFile extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public function subIdxBatch()
    {
        return $this->belongsTo(SubIdxBatch::class);
    }
}
