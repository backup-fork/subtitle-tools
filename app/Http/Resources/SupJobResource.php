<?php

namespace App\Http\Resources;

use App\Models\SupJob;

class SupJobResource extends BaseResource
{
    public function format(SupJob $supJob)
    {
        return [
            'id' => $supJob->id,
            'ocrLanguage' => $supJob->ocr_language,
            'originalName' => $supJob->original_name,
            'isFinished' => $supJob->is_finished,
            'errorMessage' => $supJob->has_error ? __($supJob->error_message) : false,
        ];
    }
}
