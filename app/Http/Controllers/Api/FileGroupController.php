<?php

namespace App\Http\Controllers\Api;

use App\Models\FileGroup;
use App\Models\FileJob;

class FileGroupController
{
    public function show($urlKey)
    {
        return FileGroup::query()
            ->where('url_key', $urlKey)
            ->firstOrFail()
            ->fileJobs
            ->map(function (FileJob $fileJob) {
                return $fileJob->getApiValues();
            });
    }
}
