<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TextFile;
use App\Subtitles\WithFileContent;

class PlainText extends TextFile
{
    use WithFileContent;

    protected $extension = 'txt';

    public function __construct()
    {
        //
    }

    public function setContent($string)
    {
        $this->content = $string;

        return $this;
    }

    public static function isThisFormat($file)
    {
        return is_text_file($file);
    }
}
