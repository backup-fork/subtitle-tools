<?php

namespace App\Http\Resources;

use App\Models\SubIdx;

class SubIdxResource extends BaseResource
{
    public function format(SubIdx $subIdx)
    {
        return [
            'id' => $subIdx->id,
            'originalName' => $subIdx->original_name,
            'downloadZipUrl' => route('subIdx.downloadZip', $subIdx->url_key),
            'languages' => SubIdxLanguageResource::collection($subIdx->languages),
        ];
    }
}
