<?php

namespace App\Http\Resources;

use App\Models\SubIdxLanguage;

class SubIdxLanguageResource extends BaseResource
{
    public function format(SubIdxLanguage $language)
    {
        return [
            'id' => $language->id,
            'index' => $language->index,
            'language' => __("languages.subIdx.$language->language"),
            'hasError' => $language->error_message !== null,
            'canBeRequested' => $language->queued_at === null,
            'isQueued' => $language->is_queued,
            'queuePosition' => $language->queue_position,
            'isProcessing' => $language->is_processing,
            'downloadUrl' => $language->download_url,
        ];
    }
}
