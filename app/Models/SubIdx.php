<?php

namespace App\Models;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use App\Support\Facades\VobSub2Srt;
use App\Support\Utils\FileName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubIdx extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sub_file_size' => 'int',
        'idx_file_size' => 'int',
        'is_readable' => 'bool',
        'last_cache_hit' => 'datetime',
        'cache_hits' => 'int',
    ];

    public function languages()
    {
        return $this->hasMany(SubIdxLanguage::class);
    }

    public function batch()
    {
        return $this->belongsTo(SubIdxBatch::class);
    }

    public function getFilePathWithoutExtensionAttribute()
    {
        return storage_disk_file_path($this->store_directory.$this->filename);
    }

    public static function getOrCreateFromUpload(UploadedFile $subFile, UploadedFile $idxFile)
    {
        [$subHash, $idxHash] = file_hash($subFile, $idxFile);

        $cachedSubIdx = SubIdx::query()
            ->where('sub_hash', $subHash)
            ->where('idx_hash', $idxHash)
            ->first();

        SubIdxStats::recordNewUpload($subFile, $idxFile, (bool) $cachedSubIdx);

        if ($cachedSubIdx) {
            // Don't update the "updated_at" column, that column is used in "RandomizeSubIdxUrlKeysJob".
            $cachedSubIdx->timestamps = false;

            $cachedSubIdx->increment('cache_hits', 1, ['last_cache_hit' => now()]);

            $cachedSubIdx->timestamps = true;

            return $cachedSubIdx;
        }

        $baseFileName = substr($subHash, 0, 6).substr($idxHash, 0, 6);

        // The date in this path is used in the "PruneSubIdxFiles" job
        $storagePath = 'sub-idx/'.now()->format('Y-z/U')."-{$baseFileName}/";

        Storage::makeDirectory($storagePath);

        $destinationFilePathWithoutExtension = storage_disk_file_path($storagePath.$baseFileName);

        copy($subFile->getRealPath(), "$destinationFilePathWithoutExtension.sub");
        copy($idxFile->getRealPath(), "$destinationFilePathWithoutExtension.idx");

        $languages = VobSub2Srt::path($destinationFilePathWithoutExtension)->languages();

        $subIdx = SubIdx::create([
            'original_name' => name_without_extension($subFile),
            'store_directory' => $storagePath,
            'filename' => $baseFileName,
            'sub_hash' => $subHash,
            'idx_hash' => $idxHash,
            'sub_file_size' => filesize($destinationFilePathWithoutExtension.'.sub'),
            'idx_file_size' => filesize($destinationFilePathWithoutExtension.'.idx'),
            'is_readable' => $isReadable = (bool) $languages,
            'url_key' => $isReadable ? generate_url_key() : null,
        ]);

        $subIdx->languages()->createMany($languages);

        SubIdxLanguageStats::recordForNewUpload($subIdx);

        if (count($languages) === 1) {
            $subIdx->languages->first()->queueExtractJob();
        }

        return $subIdx;
    }

    public static function createFromBatchFile(SubIdxBatchFile $batchFile, array $extractLanguages)
    {
        $storageDirectory = (new FileName)->getWithoutExtension($batchFile->sub_storage_file_path);

        $destinationFilePathWithoutExtension = storage_disk_file_path($storageDirectory);

        $subIdx = SubIdx::create([
            'sub_idx_batch_id' => $batchFile->sub_idx_batch_id,
            'original_name' => $batchFile->sub_original_name,
            'store_directory' => rtrim($storageDirectory, 'a'),
            'filename' => 'a',
            'sub_hash' => sha1(Str::random()),
            'idx_hash' => sha1(Str::random()),
            'sub_file_size' => filesize($destinationFilePathWithoutExtension.'.sub'),
            'idx_file_size' => filesize($destinationFilePathWithoutExtension.'.idx'),
            'is_readable' => true,
            'url_key' => generate_url_key(),
        ]);

        $languages = array_filter($batchFile->languages(), function ($array) use ($extractLanguages) {
            return in_array($array['language'], $extractLanguages);
        });

        $subIdx->languages()->createMany($languages);

        return $subIdx;
    }
}
