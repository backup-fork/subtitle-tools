<?php

namespace App\Support\Utils;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TempDir
{
    public function make($identifier = 'temp')
    {
        $newDirectoryName = date('Y-z-').$identifier.'-'.Str::random(16);

        Storage::makeDirectory('temporary-dirs/'.$newDirectoryName);

        return storage_disk_file_path('temporary-dirs/').$newDirectoryName;
    }
}
