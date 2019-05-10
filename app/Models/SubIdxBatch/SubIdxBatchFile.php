<?php

namespace App\Models\SubIdxBatch;

use App\Support\Facades\VobSub2Srt;
use App\Support\Utils\FileName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SubIdxBatchFile extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public function subIdxBatch()
    {
        return $this->belongsTo(SubIdxBatch::class);
    }

    public function languages()
    {
        return Cache::rememberForever($this->id, function () {
            $path = (new FileName)->getWithoutExtension($this->sub_storage_file_path);

            $languages = VobSub2Srt::path(storage_disk_file_path($path))->languages();

            $languageCodes = [];

            foreach ($languages as $language) {
                $languageCodes[] = $language['language'];
            }

            return $languageCodes;
        });
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
