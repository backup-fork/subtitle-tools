<?php

namespace App\Http\Controllers\Api;

use App\Jobs\ZipFileGroupJob;
use App\Models\FileGroup;

class FileGroupArchiveController
{
    public function show($urlKey)
    {
        return FileGroup::query()
            ->where('url_key', $urlKey)
            ->firstOrFail()
            ->getApiValues();
    }

    public function request($urlKey)
    {
        $fileGroup = FileGroup::query()
            ->where('url_key', $urlKey)
            ->whereNotNull('file_jobs_finished_at')
            ->whereNull('archive_requested_at')
            ->firstOrFail();

        ZipFileGroupJob::dispatch($fileGroup);

        $fileGroup->update(['archive_requested_at' => now()]);
    }
}
