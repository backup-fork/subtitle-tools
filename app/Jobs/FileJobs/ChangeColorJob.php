<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\ChangesColor;
use App\Subtitles\TextFile;
use App\Subtitles\Tools\Options\ChangeColorOptions;

class ChangeColorJob extends FileJob
{
    protected $newExtension = null;

    public function process(TextFile $subtitle, $options)
    {
        if (! $subtitle instanceof ChangesColor) {
            $this->abort('messages.file_can_not_change_color');
        }

        $this->newExtension = $subtitle->getExtension();

        /** @var $options ChangeColorOptions */
        $subtitle->changeColor($options->newColor);

        return $subtitle;
    }
}
