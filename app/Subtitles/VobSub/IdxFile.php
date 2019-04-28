<?php

namespace App\Subtitles\VobSub;

class IdxFile
{
    protected $indexLanguageArray = [];

    public function __construct($idxFilePath)
    {
        foreach (read_lines($idxFilePath) as $line) {
            if (preg_match('/^id: (?<lang>[a-z]+), index: (?<id>\d+)$/', $line, $match)) {
                $this->indexLanguageArray[$match['id']] = $match['lang'];
            }
        }
    }

    public function getLanguageForIndex($index)
    {
        return $this->indexLanguageArray[$index] ?? 'unknown';
    }
}
