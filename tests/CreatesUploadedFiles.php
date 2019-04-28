<?php

namespace Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait CreatesUploadedFiles
{
    public function createUploadedFile($filePath, $fileName = null)
    {
        if (! Str::startsWith($filePath, $this->testFilesStoragePath)) {
            $filePath = Str::finish($this->testFilesStoragePath, '/').ltrim($filePath, '/');
        }

        return new UploadedFile(
            $filePath,
            $fileName ?? base_path($filePath),
            null,
            null,
            null,
            true
        );
    }

    public function createUploadedFiles($files)
    {
        return array_map(function ($file) {
            return $this->createUploadedFile($file);
        }, $files);
    }
}
