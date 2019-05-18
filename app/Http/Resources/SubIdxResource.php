<?php

namespace App\Http\Resources;

use App\Models\SubIdx;
use Illuminate\Http\Resources\Json\Resource;

class SubIdxResource extends Resource
{
    public function toArray($request)
    {
        /** @var SubIdx $subIdx */
        $subIdx = $this->resource;

        return [
            'id' => $subIdx->id,
            'originalName' => $subIdx->original_name,
            'languages' => SubIdxLanguageResource::collection($subIdx->languages),
        ];
    }
}
