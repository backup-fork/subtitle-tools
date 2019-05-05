<?php

namespace App\Models\SubIdxBatch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SubIdxBatchFile extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public function subIdxBatch()
    {
        return $this->belongsTo(SubIdxBatch::class);
    }

    public function resolveRouteBinding($value)
    {
        $batchFile = parent::resolveRouteBinding($value);

        if ($batchFile && $batchFile->subIdxBatch->user_id !== Auth::id()) {
            abort(403);
        }

        return $batchFile;
    }
}
