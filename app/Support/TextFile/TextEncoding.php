<?php

namespace App\Support\TextFile;

use RuntimeException;
use App\Support\TextFile\Exceptions\TextEncodingException;

class TextEncoding
{
    protected $fallbackEncoding = null;

    /**
     * "uchardet encoding name" => "php encoding name"
     *
     * @var array
     */
    protected $allowedEncodings = [
        'unknown' => 'UTF-8', // default to UTF-8
        'ascii' => 'UTF-8', // default to UTF-8
        'ascii/unknown' => 'UTF-8', // default to UTF-8
        'big5' => 'Big5', // Traditional Chinese
        'euc-jp' => 'EUC-JP',
        'euc-kr' => 'EUC-KR', // Korean
        'euc-tw' => 'EUC-TW',
        'gb18030' => 'gb18030', // Simplified Chinese
        'gb2312' => 'gb2312', // Simplified Chinese
        'hz-gb-2312' => 'HZ', // Simplified Chinese
        'ibm855' => 'IBM855',
        'ibm866' => 'IBM866',
        'iso-2022-jp' => 'ISO-2022-JP',
        'iso-8859-1' => 'ISO-8859-1',
        'iso-8859-2' => 'ISO-8859-2', // Romanian (gets detected as windows-1252)
        'iso-8859-3' => 'ISO-8859-3',
        'iso-8859-5' => 'ISO-8859-5',
        'iso-8859-7' => 'ISO-8859-7', // Greek, almost identical to "windows-1253"
        'iso-8859-8' => 'ISO-8859-8',
        'iso-8859-9' => 'ISO-8859-9', // Turkish
        'iso-8859-11' => 'ISO-8859-11',
        'iso-8859-15' => 'ISO-8859-15',
        'koi8-r' => 'KOI8-R',
        'shift_jis' => 'Shift_JIS', // Japanese
        'tis-620' => 'TIS-620', // Thai
        'utf-16' => 'UTF-16',
        'utf-8' => 'UTF-8',
        'utf-32' => 'UTF-32',
        'viscii' => 'viscii',
        'windows-1250' => 'windows-1250', // ANSI (for Polish, doesn't work for scandinavian languages)
        'windows-1251' => 'windows-1251', // Russian
        'windows-1252' => 'windows-1252', // ANSI (for scandinavian languages, doesn't work for Polish)
        'windows-1253' => 'windows-1253', // Greek, almost identical to "iso-8859-7"
        'windows-1255' => 'windows-1255',
        'windows-1256' => 'windows-1256',
        'windows-1258' => 'windows-1258',
        'mac-cyrillic' => 'MacCyrillic',
        'x-mac-cyrillic' => 'MacCyrillic',
    ];

    /**
     * List of php encoding names that should be converted using the "iconv()"
     * function instead of the "mb_convert_encoding()" function.
     *
     * @var array
     */
    protected $iconvEncodings = [
        'IBM855',
        'ISO-8859-11',
        'MacCyrillic',
        'TIS-620',
        'viscii',
        'windows-1250',
        'windows-1253',
        'windows-1255',
        'windows-1256',
        'windows-1258',
    ];

    public function __construct($fallbackEncoding = null)
    {
        $this->fallbackEncoding = $fallbackEncoding;
    }

    public function detect($string)
    {
        $tempFileHandle = tmpfile();

        fwrite($tempFileHandle, $string);

        return $this->detectFromFile(stream_get_meta_data($tempFileHandle)['uri']);
    }

    public function detectFromFile($filePath)
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException('File does not exist :'.$filePath);
        }

        $encoding = strtolower(trim(
            shell_exec("uchardet \"{$filePath}\"")
        ));

        if (empty($encoding)) {
            return $this->allowedEncodings['ascii/unknown'];
        }

        if (! $this->isAllowedEncoding($encoding)) {
            if ($this->fallbackEncoding) {
                return $this->fallbackEncoding;
            }

            throw new TextEncodingException("Detected: {$encoding}, was not on the whitelist");
        }

        $encodingName = $this->allowedEncodings[$encoding];

        // When uchardet detects one of the encodings below, some manual work
        // is needed to figure out the correct encoding.
        if ($encodingName === 'windows-1252') {
            $encodingName = $this->getCorrect1252Encoding($filePath);
        } elseif ($encodingName === 'ISO-8859-3') {
            $encodingName = $this->getCorrectIso8859_3Encoding($filePath);
        } elseif ($encodingName === 'MacCyrillic') {
            $encodingName = $this->getCorrectMacCyrillicEncoding($filePath);
        }

        return $encodingName;
    }

    public function toUtf8($string, $inputEncoding = null): string
    {
        return $this->to($string, 'UTF-8', $inputEncoding);
    }

    protected function to($string, $outputEncoding, $inputEncoding = null): string
    {
        $inputEncoding = $inputEncoding ?? $this->detect($string);

        // Remove the BOM from files encoded in utf-8
        if (stripos($inputEncoding, 'utf-8') === 0) {
            $utf8Bom = pack('H*', 'EFBBBF');

            if (preg_match("/^{$utf8Bom}/", $string)) {
                $string = preg_replace("/^{$utf8Bom}/", '', $string);
            }
        }

        if ($this->isIconvEncoding($inputEncoding)) {
            return iconv($inputEncoding, "{$outputEncoding}//IGNORE", $string);
        }

        return mb_convert_encoding($string, $outputEncoding, $inputEncoding);
    }

    protected function isAllowedEncoding($encoding)
    {
        return isset($this->allowedEncodings[$encoding]);
    }

    protected function isIconvEncoding($encoding)
    {
        return in_array($encoding, $this->iconvEncodings);
    }

    protected function getCorrect1252Encoding($filePath)
    {
        $content = file_get_contents($filePath);

        if (strpos($content, "\xB3") !== false) {
            // B3 hex in windows-1252 === ³ (cube)
            // B3 hex in windows-1254 === ³ (cube)
            // B3 hex in windows-1250 === ł (polish letter)
            return 'windows-1250';
        } elseif (strpos($content, "\xBA") !== false) {
            // BA hex in windows-1252 === º (degree sign)
            // BA hex in windows-1254 === º (degree sign)
            // BA hex in   ISO-8859-2 === ş (romanian letter)
            return 'ISO-8859-2';
        }

        return 'windows-1252';
    }

    private function getCorrectMacCyrillicEncoding($filePath)
    {
        $content = file_get_contents($filePath);

        if (substr_count($content, "\xA1") > 2) {
            // A1 hex in windows-1256 === ﺧ (something Persian)
            // A1 hex in MacCyrillic  === ° (degree sign)
            return 'windows-1256';
        }

        $windows1256Content = $this->toUtf8($content, 'windows-1256');

        // \xA7
        if (strpos($windows1256Content, 'ﺱ') !== false) {
            return 'windows-1256';
        }

        return 'MacCyrillic';
    }

    private function getCorrectIso8859_3Encoding($filePath)
    {
        $content = file_get_contents($filePath);

        if (strpos($content, "\xB3") !== false) {
            // B3 hex in   iso-8859-3 === ³ (cube)
            // B3 hex in windows-1250 === ł (polish letter)
            return 'windows-1250';
        }

        return 'ISO-8859-3';
    }
}
