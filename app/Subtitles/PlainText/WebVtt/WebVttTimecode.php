<?php

namespace App\Subtitles\PlainText\WebVtt;

class WebVttTimecode
{
    private $milliseconds;

    public function __construct($string = '00:00:00.000')
    {
        $string = trim($string);

        if (! preg_match('/^(\d{2,}: ?|)[0-5]\d: ?[0-5]\d(,|\.)\d{3}$/', $string, $matches)) {
            return;
        }

        $this->milliseconds = $this->timecodeToMs($string);
    }

    private function timecodeToMs($timecode)
    {
        $parts = preg_split('/(:|\.|,)/', $timecode);

        if (count($parts) === 4) {
            [$hours, $minutes, $seconds, $milliseconds] = $parts;
        } else {
            $hours = 0;

            [$minutes, $seconds, $milliseconds] = $parts;
        }

        return ($hours * 60 * 60 * 1000) +
            ($minutes * 60 * 1000) +
            ($seconds * 1000) +
            $milliseconds;
    }

    public function valid()
    {
        return $this->milliseconds !== null;
    }

    public function invalid()
    {
        return ! $this->valid();
    }

    public function timecode()
    {
        if ($this->milliseconds <= 0) {
            return '00:00:00.000';
        }

        $SS = floor($this->milliseconds / 1000);
        $MM = floor($SS / 60);
        $HH = floor($MM / 60);
        $MIL = $this->milliseconds % 1000;
        $SS = $SS % 60;
        $MM = $MM % 60;

        $HH = str_pad($HH, 2, '0', STR_PAD_LEFT);
        $MM = str_pad($MM, 2, '0', STR_PAD_LEFT);
        $SS = str_pad($SS, 2, '0', STR_PAD_LEFT);
        $MIL = str_pad($MIL, 3, '0', STR_PAD_LEFT);

        return "{$HH}:{$MM}:{$SS}.{$MIL}";
    }

    public function milliseconds()
    {
        return $this->milliseconds;
    }

    public function setMilliseconds($ms)
    {
        $this->milliseconds = $ms;

        return $this;
    }
}
