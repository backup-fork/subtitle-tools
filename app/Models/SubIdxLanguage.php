<?php

namespace App\Models;

use App\Jobs\ExtractSubIdxLanguageJob;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class SubIdxLanguage extends Model
{
    protected $guarded = [];

    protected $touches = ['subIdx'];

    protected $casts = [
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'times_downloaded' => 'int',
    ];

    public function subIdx()
    {
        return $this->belongsTo(SubIdx::class);
    }

    public function outputStoredFile()
    {
        return $this->hasOne(StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function getFileNameAttribute()
    {
        return sprintf('%s-%s.srt', $this->subIdx->original_name, $this->language);
    }

    public function getIsQueuedAttribute()
    {
        return $this->queued_at !== null && $this->started_at === null;
    }

    public function getIsProcessingAttribute()
    {
        return $this->started_at !== null && $this->finished_at === null;
    }

    public function getQueuePositionAttribute()
    {
        if (! $this->is_queued) {
            return null;
        }

        return SubIdxLanguage::query()
            ->whereNotNull('queued_at')
            ->whereNull('started_at')
            ->where('queued_at', '<=', $this->queued_at)
            ->count();
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->output_stored_file_id === null) {
            return false;
        }

        return route('subIdx.download', [$this->subIdx->url_key, $this->index]);
    }

    public function queueExtractJob()
    {
        if ($this->queued_at) {
            throw new RuntimeException();
        }

        $this->update(['queued_at' => now()]);

        SubIdxLanguageStats::recordLanguageExtracted($this);

        ExtractSubIdxLanguageJob::dispatch($this);
    }
}
