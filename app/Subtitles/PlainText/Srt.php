<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\ChangesColor;
use App\Subtitles\ContainsGenericCues;
use App\Subtitles\LoadsGenericSubtitles;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use App\Subtitles\WithGenericCues;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Srt extends TextFile implements LoadsGenericSubtitles, ShiftsCues, PartialShiftsCues, ContainsGenericCues, TransformsToGenericSubtitle, ChangesColor
{
    use WithFileLines, WithGenericCues;

    protected $extension = 'srt';

    /**
     * @var SrtCue[]
     */
    protected $cues = [];

    /**
     * @param $cue
     *
     * @return SrtCue
     */
    public function addCue($cue)
    {
        if (! $cue instanceof SrtCue && ! $cue instanceof GenericSubtitleCue) {
            throw new \InvalidArgumentException('Invalid cue');
        }

        $cue = $cue instanceof SrtCue
            ? $cue
            : new SrtCue($cue);

        $this->cues[] = $cue;

        return $cue;
    }

    public function createCue($startMs, $endMs, $lines)
    {
        $lines = is_array($lines) ? $lines : [$lines];

        $this->addCue(
            (new SrtCue)->setTiming($startMs, $endMs)->setLines($lines)
        );

        return $this;
    }

    public function getContentLines()
    {
        $id = 1;
        $lines = [];

        foreach ($this->getCues() as $cue) {
            $lines[] = (string) $id++;

            $lines = array_merge($lines, $cue->toArray());
        }

        return $lines;
    }

    public static function isThisFormat($file)
    {
        $lines = read_lines($file);

        for ($i = 1; $i < count($lines); $i++) {
            if (SrtCue::isTimingString($lines[$i]) && preg_match('/^\d+$/', trim($lines[$i-1]))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $file string|UploadedFile A file path or UploadedFile
     * @return $this
     */
    public function loadFile($file)
    {
        $this->originalFileNameWithoutExtension = name_without_extension($file);

        $this->filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = read_lines($this->filePath);

        $this->cues = [];

        // ensure parsing works properly on files missing the required trailing empty line
        $lines[] = "";

        $timingIndexes = [];

        for ($i = 0; $i < count($lines); $i++) {
            if (SrtCue::isTimingString($lines[$i])) {
                $timingIndexes[] = $i;
            }
        }

        $timingIndexes[] = count($lines);

        for ($timingIndex = 0; $timingIndex < count($timingIndexes) - 1; $timingIndex++) {
            $newCue = new SrtCue();

            $newCue->setTimingFromString($lines[$timingIndexes[$timingIndex]]);

            for ($lineIndex = $timingIndexes[$timingIndex] + 1; $lineIndex < $timingIndexes[$timingIndex+1] - 1; $lineIndex++) {
                $newCue->addLine($lines[$lineIndex]);
            }

            $this->AddCue($newCue);
        }

        $this->removeEmptyCues()
            ->removeDuplicateCues();

        return $this;
    }

    public function loadGenericSubtitle(GenericSubtitle $genericSubtitle)
    {
        $this->setFilePath($genericSubtitle->getFilePath());

        $this->setFileNameWithoutExtension($genericSubtitle->getFileNameWithoutExtension());

        foreach ($genericSubtitle->getCues() as $genericCue) {
            $this->addCue($genericCue);
        }

        return $this;
    }

    public function changeColor($color)
    {
        foreach ($this->cues as $cue) {
            $cue->changeColor($color);
        }

        return $this;
    }

    public function shift($ms)
    {
        foreach ($this->cues as $cue) {
            $cue->shift($ms);
        }

        return $this;
    }

    public function shiftPartial($fromMs, $toMs, $ms)
    {
        if ($fromMs > $toMs || $ms == 0) {
            return $this;
        }

        foreach ($this->cues as $cue) {
            if ($cue->getStartMs() >= $fromMs && $cue->getStartMs() <= $toMs) {
                $cue->shift($ms);
            }
        }

        return $this;
    }

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach ($this->getCues(false) as $cue) {
            $newGenericCue = new GenericSubtitleCue();

            $newGenericCue->setTiming($cue->getStartMs(), $cue->getEndMs());

            $newGenericCue->setLines($cue->getLines());

            $generic->addCue($newGenericCue);
        }

        return $generic;
    }
}
