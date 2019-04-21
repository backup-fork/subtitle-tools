<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubIdxStats extends Model
{
    protected $guarded = [];

    protected $casts = [
        'cache_hits' => 'int',
        'cache_misses' => 'int',
        'total_file_size' => 'int',
        'images_ocrd_count' => 'int',
        'milliseconds_spent_ocring' => 'int',
    ];

    public static function today()
    {
        return static::firstOrCreate([
            'date' => now()->format('Y-m-d')
        ], [
            'cache_hits' => 0,
            'cache_misses' => 0,
            'total_file_size' => 0,
            'images_ocrd_count' => 0,
            'milliseconds_spent_ocring' => 0,
        ]);
    }

    public static function recordNewUpload(UploadedFile $subFile, UploadedFile $idxFile, bool $isCacheHit)
    {
        static::today();

        $fileSize = filesize($subFile->getRealPath()) + filesize($idxFile->getRealPath());

        $cacheColumn = $isCacheHit ? 'cache_hits' : 'cache_misses';

        DB::table('sub_idx_stats')
            ->where('date', now()->format('Y-m-d'))
            ->update([
                $cacheColumn => DB::raw("$cacheColumn + 1"),
                'total_file_size' => DB::raw("total_file_size + $fileSize"),
            ]);
    }
}
