<?php

namespace App\Http\Middleware;

use App\Jobs\Diagnostic\RecordUploadedFileMimesJob;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class RecordUploadedFileMimes
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('POST')) {
            $this->recordMimes($request);
        }

        return $next($request);
    }

    private function recordMimes(Request $request)
    {
        $mimeCounts = collect($request->allFiles())
            ->flatten()
            ->mapToGroups(function (UploadedFile $file) {
                return [file_mime($file->getRealPath()) => 1];
            })
            ->map(function ($array) {
                return count($array);
            })
            ->all();

        if (count($mimeCounts) === 0) {
            return;
        }

        RecordUploadedFileMimesJob::dispatch($request->getPathInfo(), $mimeCounts);
    }
}
