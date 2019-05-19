<?php

namespace App\Support\Utils;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TempFile
{
    protected $createdFilePaths = [];

    public function make($content)
    {
        $tempFilePath = $this->makeFilePath();

        file_put_contents($tempFilePath, $content);

        register_shutdown_function (function () use ($tempFilePath) {
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        });

        $this->createdFilePaths[] = $tempFilePath;

        return $tempFilePath;
    }

    public function makeFilePath($identifier = 'temp')
    {
        $directory = storage_disk_file_path('temporary-files/');

        if (! file_exists($directory)) {
            Storage::makeDirectory('temporary-files/');
        }

        // This name is used in the PruneTemporaryFiles command
        return sprintf('%s%s-%s-%s', $directory, date('Y-z'), $identifier, Str::random(16));
    }

    public function cleanUp()
    {
        // this function is necessary because the queue worker doesn't run my shutdown functions
        foreach ($this->createdFilePaths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->createdFilePaths = [];
    }
}
