<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use App\Models\UploadedFileMime;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordUploadedFileMimesJob extends BaseJob implements ShouldQueue
{
    public $queue = 'A500';

    public $uri;

    public $mimeCounts;

    public function __construct(string $uri, array $mimeCounts)
    {
        $this->uri = $uri;

        $this->mimeCounts = $mimeCounts;
    }

    public function handle()
    {
        foreach ($this->mimeCounts as $mime => $count) {
            $exists = UploadedFileMime::query()
                ->where('uri', $this->uri)
                ->where('mime', $mime)
                ->exists();

            $exists ? $this->increment($mime, $count) : $this->create($mime, $count);
        }
    }

    private function create($mime, $count)
    {
        UploadedFileMime::create([
            'uri' => $this->uri,
            'mime' => $mime,
            'count' => $count,
        ]);
    }

    private function increment($mime, $count)
    {
        UploadedFileMime::query()
            ->where('uri', $this->uri)
            ->where('mime', $mime)
            ->increment('count', $count);
    }
}
