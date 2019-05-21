<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

abstract class BaseResource extends Resource
{
    public function toArray($request)
    {
        return $this->format($this->resource);
    }
}
