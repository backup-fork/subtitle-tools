<?php

namespace App\Subtitles\PlainText\WebVtt;

class WebVttTiming
{
    private $startTimecode;

    private $endTimecode;

    private $style;

    public function __construct($timing)
    {
        $timing = strtolower(
            trim($timing)
        );

        if (! strpos($timing, '->')) {
            return;
        }

        [$startTimecode, $rest] = preg_split('/-?->/', $timing, 2);

        [$endTimecode, $style] = preg_split('/(?<=(,|\.)\d{3})/', $rest, 2);

        $this->startTimecode = new WebVttTimecode($startTimecode);

        $this->endTimecode = new WebVttTimecode($endTimecode);

        $this->style = trim($style);
    }

    public function startMs()
    {
        return $this->startTimecode->milliseconds();
    }

    public function endMs()
    {
        return $this->endTimecode->milliseconds();
    }

    public function style()
    {
        return $this->style;
    }

    public function toString()
    {
        $timing = $this->startTimecode->timecode().' --> '.$this->endTimecode->timecode();

        if ($this->style) {
            $timing = $timing.' '.$this->style;
        }

        return $timing;
    }

    public function valid()
    {
        if (! $this->startTimecode || ! $this->endTimecode) {
            return false;
        }

        if ($this->startTimecode->invalid() || $this->endTimecode->invalid()) {
            return false;
        }

        if ($this->startTimecode->milliseconds() > $this->endTimecode->milliseconds()) {
            return false;
        }

        if (strpos($this->style, '-->') !== false) {
            return false;
        }

        return true;
    }

    public function invalid()
    {
        return ! $this->valid();
    }
}
