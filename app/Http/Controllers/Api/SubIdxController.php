<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SubIdxLanguageResource;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use Illuminate\Database\Eloquent\Builder;

class SubIdxController
{
    public function languages($urlKey)
    {
        $subIdx = SubIdx::query()
            ->with('languages')
            ->where('url_key', $urlKey)
            ->firstOrFail();

        return SubIdxLanguageResource::collection($subIdx->languages);
    }

    public function extractLanguage($urlKey, $languageId)
    {
        SubIdxLanguage::query()
            ->where('id', $languageId)
            ->whereNull('queued_at')
            ->whereHas('subIdx', function (Builder $query) use ($urlKey) {
                $query->where('url_key', $urlKey);
            })
            ->firstOrFail()
            ->queueExtractJob();
    }
}
