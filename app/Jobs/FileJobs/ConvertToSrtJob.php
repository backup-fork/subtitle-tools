<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\TextFile;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\TransformsToGenericSubtitle;

class ConvertToSrtJob extends FileJob
{
    protected $newExtension = 'srt';

    public function process(TextFile $subtitle, $options)
    {
        // Srt input files are allowed because:
        // * Sometimes files are uploaded that are srt files, but have the wrong extension
        // * Sometimes zip files are uploaded that contain srt files (from people who don't understand what an archive file is)
        // * Sometimes people upload srt files, simply not understanding the point of this tool
        if (! $subtitle instanceof TransformsToGenericSubtitle) {
            $this->abort('messages.cant_convert_file_to_srt');
        }

        $srt = $subtitle instanceof Srt ? $subtitle : new Srt($subtitle);

        $srt->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        if (! $srt->hasCues()) {
            $this->abort('messages.file_has_no_dialogue_to_convert');
        }

        return $srt;
    }
}
