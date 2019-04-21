<?php

namespace App\Models;

use App\Support\Facades\TempFile;
use App\Subtitles\TextFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class StoredFile extends Model
{
    protected $guarded = [];

    public function meta()
    {
        return $this->hasOne(StoredFileMeta::class);
    }

    public function getFilePathAttribute()
    {
        return storage_disk_file_path($this->storage_file_path);
    }

    public static function getOrCreate($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $hash = file_hash($filePath);

        $storedFileFromCache = StoredFile::query()->where('hash', $hash)->first();

        if ($storedFileFromCache) {
            return $storedFileFromCache;
        }

        $storagePath = 'stored-files/'.now()->format('Y-W/z');

        if (! File::isDirectory($storagePath)) {
            Storage::makeDirectory($storagePath);
        }

        $storageFilePath = $storagePath.now()->format('/U-').substr($hash, 0, 16);

        // copy instead of moving to prevent from moving test files
        copy($filePath, storage_disk_file_path($storageFilePath));

        try {
            return StoredFile::create([
                'hash' => $hash,
                'storage_file_path' => $storageFilePath,
            ]);
        } catch (\PDOException $e) {
            // Due to having multiple queue workers, a race condition can
            // cause a "duplicate entry" unique constraint failure.
            if (stripos($e->getMessage(), '1062 Duplicate entry') === false) {
                throw $e;
            }

            // The race condition can never happen when running tests.
            if (app()->environment('testing')) {
                throw $e;
            }

            return StoredFile::query()->where('hash', $hash)->firstOr(function () {
                throw new RuntimeException('Unable to create StoredFile due to unique key constraint, also unable to retrieve from database (???)');
            });
        }
    }

    public static function createFromTextFile(TextFile $textFile)
    {
        $filePath = TempFile::make("\xEF\xBB\xBF".$textFile->getContent());

        return self::getOrCreate($filePath);
    }
}
