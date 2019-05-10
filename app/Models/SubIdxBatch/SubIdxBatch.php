<?php

namespace App\Models\SubIdxBatch;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function batchFileLanguages()
    {
        $languages = collect();

        foreach ($this->files as $subIdxBatchFile) {
            $languages[] = $subIdxBatchFile->languages();
        };

        return $languages->flatten()
            ->countBy()
            ->mapWithKeys(function ($count, $langCode) {
                return [__("languages.subIdx.$langCode") => [$langCode, $count]];
            })
            ->sortBy(function ($value, $key) {
                return $key;
            })
            ->all();
    }

    public function resolveRouteBinding($value)
    {
        $subIdxBatch = parent::resolveRouteBinding($value);

        if ($subIdxBatch && $subIdxBatch->user_id !== Auth::id()) {
            abort(403);
        }

        return $subIdxBatch;
    }
}
