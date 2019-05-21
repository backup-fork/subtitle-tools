<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Redirects
{
    public function __invoke(Request $request)
    {
        $destination = [
            'format-converter' => route('convertToSrt'),
            'convert-to-srt' => route('convertToSrt'),
            'fo...' => route('convertToSrt'),
            'convert-to-srt-on...' => route('convertToSrt'),
            'c...' => route('convertToSrt'),
            'tools' => '/',
            'chinese-to-pinyin' => route('pinyin'),
            'subtitle-shift' => route('shift'),
            'partial-subtitle-shifter' => route('shiftPartial'),
            'multi-subtitle-shift' => route('shiftPartial'),
            'convert-to-utf8' => route('convertToUtf8'),
            'convert-sub-idx-to-srt' => route('subIdx'),
        ][$request->path()];

        return redirect()->to($destination)->setStatusCode(301);
    }
}
