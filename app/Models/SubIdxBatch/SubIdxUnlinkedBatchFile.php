<?php

namespace App\Models\SubIdxBatch;

use Illuminate\Database\Eloquent\Model;

class SubIdxUnlinkedBatchFile extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public function subIdxBatch()
    {
        return $this->belongsTo(SubIdxBatch::class);
    }
}
