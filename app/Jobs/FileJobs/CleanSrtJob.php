<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\TextFile;
use App\Subtitles\Tools\Options\SrtCleanerOptions;
use App\Subtitles\Tools\SrtCleaner;
use App\Subtitles\PlainText\Srt;

class CleanSrtJob extends FileJob
{
    protected $newExtension = 'srt';

    protected $options = SrtCleanerOptions::class;

    public function process(TextFile $subtitle, $options)
    {
        if (! $subtitle instanceof Srt) {
            $this->abort('messages.file_is_not_srt');
        }

        (new SrtCleaner)->clean($subtitle, $options);

        if (! $subtitle->hasCues()) {
            $this->abort('messages.file_has_no_dialogue');
        }

        return $subtitle;
    }
}
