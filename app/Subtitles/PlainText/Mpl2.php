<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;

class Mpl2 extends TextFile implements TransformsToGenericSubtitle
{
    use WithFileLines;

    protected $extension = 'mpl';

    /**
     * Returns true if the $filePath file is a valid format for this class
     * @param $file
     * @return bool
     */
    public static function isThisFormat($file)
    {
        $lines = read_lines($file);

        $validCues = 0;

        foreach ($lines as $line) {
            if (Mpl2Cue::isTimingString($line)) {
                $validCues++;

                if ($validCues === 3) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach ($this->lines as $line) {
            if (Mpl2Cue::isTimingString($line)) {
                $genericCue = (new Mpl2Cue($line))->toGenericCue();

                $generic->addCue($genericCue);
            }
        }

        return $generic;
    }
}
