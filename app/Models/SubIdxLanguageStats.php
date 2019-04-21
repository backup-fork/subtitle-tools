<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubIdxLanguageStats extends Model
{
    protected $guarded = [];

    protected $casts = [
        'times_seen' => 'int',
        'times_extracted' => 'int',
    ];

    public static function recordForNewUpload(SubIdx $subIdx)
    {
        $subIdx->languages
            ->pluck('language')
            ->mapToGroups(function ($language) {
                return [$language => 1];
            })
            ->map(function ($array) {
                return count($array);
            })
            ->each(function ($count, $language) {
                static::where('language', $language)->exists()
                    ? static::where('language', $language)->increment('times_seen', $count)
                    : static::create(['language' => $language, 'times_seen' => $count]);
            });
    }

    public static function recordLanguageExtracted(SubIdxLanguage $subIdxLanguage)
    {
        static::where('language', $subIdxLanguage->language)->increment('times_extracted');
    }
}
