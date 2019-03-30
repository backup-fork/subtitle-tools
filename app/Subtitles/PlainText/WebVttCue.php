<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\LoadsGenericCues;
use App\Subtitles\PlainText\WebVtt\WebVttTimecode;
use App\Subtitles\PlainText\WebVtt\WebVttTiming;
use App\Subtitles\TimingStrings;
use LogicException;
use RuntimeException;

class WebVttCue extends GenericSubtitleCue implements TimingStrings, LoadsGenericCues
{
    private $index = '';

    private $timingStyle = '';

    public function __construct($source = null)
    {
        if ($source instanceof GenericSubtitleCue) {
            $this->loadGenericCue($source);
        } elseif ($source !== null) {
            throw new RuntimeException('Invalid VttCue source');
        }
    }

    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    public function setTimingFromString($string)
    {
        $timing = new WebVttTiming($string);

        if ($timing->invalid()) {
            throw new RuntimeException('Not a valid timing string: '.$string);
        }

        $this->setTiming(
            $timing->startMs(),
            $timing->endMs()
        );

        $this->setTimingStyle(
            $timing->style()
        );

        return $this;
    }

    public function setTimingStyle($string)
    {
        $this->timingStyle = trim($string);
    }

    public function getTimingString()
    {
        $start = (new WebVttTimecode)->setMilliseconds($this->startMs);

        $end = (new WebVttTimecode)->setMilliseconds($this->endMs);

        $timingStyle = $this->timingStyle ? ' '.$this->timingStyle : '';

        return $start->timecode().' --> '.$end->timecode().$timingStyle;
    }

    public function toArray()
    {
        $lines = [];

        if (! blank($this->index)) {
            $lines[] = $this->index;
        }

        $lines[] = $this->getTimingString();

        foreach ($this->lines as $line) {
            $lines[] = $line;
        }

        $lines[] = '';

        return $lines;
    }

    public static function isTimingString($string)
    {
        $timing = new WebVttTiming($string);

        return $timing->valid();
    }

    public function loadGenericCue(GenericSubtitleCue $genericCue)
    {
        $this->setTiming(
            $genericCue->getStartMs(),
            $genericCue->getEndMs()
        );

        $this->setLines($genericCue->getLines());

        return $this;
    }

    public function stylePositionTop()
    {
        if (count($this->lines) === 0) {
            throw new LogicException('A cue with no lines cannot be styled');
        }

        $this->lines[0] = str_start($this->lines[0], '{\an8}');

        return $this;
    }
}
