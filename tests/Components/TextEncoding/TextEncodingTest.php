<?php

namespace Tests\Components\TextFile;

use Exception;
use App\Support\TextFile\Exceptions\TextEncodingException;
use App\Support\TextFile\TextEncoding;
use Tests\TestCase;

class TextEncodingTest extends TestCase
{
    private $path;

    /** @test */
    function it_detects_encoding_for_windows_1255()
    {
        $this->assertEncoding('windows-1255', 'windows-1255/windows-1255-000-heb.txt');
    }

    /** @test */
    function it_detects_encoding_for_euc_tw()
    {
        $this->assertEncoding('EUC-TW', 'euc-tw/euc-tw-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1251()
    {
        $this->assertEncoding('windows-1251', 'windows-1251/windows-1251-000-bul.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1253()
    {
        $this->assertEncoding('windows-1253', 'windows-1253/windows-1253-000-gre.txt');
        $this->assertEncoding('windows-1253', 'windows-1253/windows-1253-001-gre.txt');
        $this->assertEncoding('windows-1253', 'windows-1253/windows-1253-002-gre.txt');
        $this->assertEncoding('windows-1253', 'windows-1253/windows-1253-003-gre.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_7()
    {
        $this->assertEncoding('ISO-8859-7', 'iso-8859-7/iso-8859-7-000-gre.txt');

        // Server detected this as "koi8-r", but local env detects "iso-8859-7"
        $this->assertEncoding('ISO-8859-7', 'iso-8859-7/iso-8859-7-001.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_3()
    {
        $this->assertEncoding('ISO-8859-3', 'iso-8859-3/iso-8859-3-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_utf_8()
    {
        $this->assertEncoding('UTF-8', 'utf-8/utf-8-000-chi.txt');
        $this->assertEncoding('UTF-8', 'utf-8/utf-8-001.txt');
    }

    /** @test */
    function it_detects_encoding_for_utf_16()
    {
        $this->assertEncoding('UTF-16', 'utf-16/utf-16-000-chi.txt');
    }

    /** @test */
    function it_detects_encoding_for_tis_620()
    {
        $this->assertEncoding('TIS-620', 'tis-620/tis-620-000-tha.txt');
    }

    /** @test */
    function it_detects_encoding_for_shift_jis()
    {
        $this->assertEncoding('Shift_JIS', 'shift-jis/shift-jis-000-jpn.txt');
    }

    /** @test */
    function it_detects_encoding_for_utf32()
    {
        // this file is mostly NULL bytes
        $this->assertEncoding('UTF-32', 'utf-32/utf-32-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_big5()
    {
        $this->assertEncoding('Big5', 'big5/big5-001-zho.txt');
    }

    /** @test */
    function it_detects_encoding_for_euc_jp()
    {
        $this->assertEncoding('EUC-JP', 'euc-jp/euc-jp-001.txt');
    }

    /** @test */
    function it_detects_encoding_for_euc_kr()
    {
        $this->assertEncoding('EUC-KR', 'euc-kr/euc-kr-001-kor.txt');
    }

    /** @test */
    function it_detects_encoding_for_gb18030()
    {
        $this->assertEncoding('gb18030', 'gb18030/gb18030-001-chi.txt');
    }

    /** @test */
    function it_detects_encoding_for_ibm866()
    {
        $this->assertEncoding('IBM866', 'ibm866/ibm866-000.txt');
        $this->assertEncoding('IBM866', 'ibm866/ibm866-001.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_2022_jp()
    {
        $this->assertEncoding('ISO-2022-JP', 'iso-2022-jp/iso-2022-jp-001-jpn.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_1()
    {
        $this->assertEncoding('ISO-8859-1', 'iso-8859-1/iso-8859-1-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_2()
    {
        // ron = Romanian
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-000-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-001-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-002-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-003-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-004-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-005-ron.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_9()
    {
        $this->assertEncoding('ISO-8859-9', 'iso-8859-9/iso-8859-9-000-tur.txt');
        $this->assertEncoding('ISO-8859-9', 'iso-8859-9/iso-8859-9-001-tur.txt');
        $this->assertEncoding('ISO-8859-9', 'iso-8859-9/iso-8859-9-002-tur.txt');
        $this->assertEncoding('ISO-8859-9', 'iso-8859-9/iso-8859-9-003-tur.txt');
        $this->assertEncoding('ISO-8859-9', 'iso-8859-9/iso-8859-9-004-tur.txt');
        $this->assertEncoding('ISO-8859-9', 'iso-8859-9/iso-8859-9-005-tur.txt');
        $this->assertEncoding('ISO-8859-9', 'iso-8859-9/iso-8859-9-006-tur.txt');
        $this->assertEncoding('ISO-8859-9', 'iso-8859-9/iso-8859-9-007-tur.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_11()
    {
        // Not the right encoding for this file, but uchardet detects it as "ISO-8859-11"
        $this->assertEncoding('ISO-8859-11', 'iso-8859-11/iso-8859-11-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_koi8_r()
    {
        // no example files yet. Server detects files as "koi8-r", local env says "iso-8859-7"
    }

    /** @test */
    function it_detects_encoding_for_windows_1250()
    {
        $this->assertEncoding('windows-1250', 'windows-1250/windows-1250-000-pol.txt');
        $this->assertEncoding('windows-1250', 'windows-1250/windows-1250-001-pol.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1252()
    {

    }

    /** @test */
    function it_detects_encoding_for_windows_1258()
    {
        // No special characters
        $this->assertEncoding('windows-1258', 'windows-1258/windows-1258-001-eng.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_15()
    {
        $this->assertEncoding('ISO-8859-15', 'iso-8859-15/iso-8859-15-000-dan.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1256()
    {
        // fas = per = Persian
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-000-fas.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-001-fas.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-002-fas.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-003-fas.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-004.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-005.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-006.txt');
    }

    /** @test */
    function it_detects_encoding_for_mac_cyrillic()
    {
        $this->assertEncoding('MacCyrillic', 'mac-cyrillic/mac-cyrillic-000-rus.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_5()
    {
        // this is a ".nfo" file
        $this->assertEncoding('ISO-8859-5', 'iso-8859-5/iso-8859-5-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_hz()
    {
        // might be a false-positive
        $this->assertEncoding('HZ', 'hz/hz-000-eng.txt');
    }

    /** @test */
    function it_detects_encoding_for_viscii()
    {
        $this->assertEncoding('viscii', 'viscii/viscii-000.txt');
    }

    /** @test */
    function it_detects_encodings_from_strings()
    {
        $string = file_get_contents($this->path.'big5/big5-001-zho.txt');

        $this->assertSame(
            'Big5',
            (new TextEncoding)->detect($string)
        );
    }

    /** @test */
    function it_ignores_illegal_characters_when_using_iconv()
    {
        $string = file_get_contents($this->path.'iconv-illegal-chars.txt');

        $output = (new TextEncoding)->toUtf8($string);

        $this->assertTrue(strlen($output) > 10);
    }

    /** @test */
    function it_throws_a_text_encoding_exception_when_it_cant_detect_the_encoding()
    {
        $this->expectException(TextEncodingException::class);

        $mockTextEncoding = new class extends TextEncoding {
            protected $allowedEncodings = [];
        };

        $mockTextEncoding->detectFromFile($this->path.'big5/big5-001-zho.txt');
    }

    /** @test */
    function it_uses_a_fallback_if_specified_when_it_cant_detect_the_encoding()
    {
        $mock = new class('fallback-encoding') extends TextEncoding {
            protected $allowedEncodings = [];
        };

        $this->assertSame(
            'fallback-encoding',
            $mock->detectFromFile($this->path.'big5/big5-001-zho.txt')
        );
    }

    private function assertEncoding($expected, $fileName)
    {
        $filePath = $this->path.ltrim($fileName, DIRECTORY_SEPARATOR);

        $textEncoding = new TextEncoding();

        $this->assertSame(
            $expected,
            $textEncoding->detectFromFile($filePath),
            'File: '.$fileName
        );

        $string = file_get_contents($filePath);

        try {
            $output = $textEncoding->toUtf8($string);
        } catch (Exception $exception) {
            $this->fail("Could not convert $expected to UTF-8.\n$fileName\n".$exception->getMessage());

            return;
        }

        $this->assertTrue(strlen($output) > 100);
    }

    public function settingUp()
    {
        $this->path = base_path('tests/Components/TextEncoding/Files/');

        $this->testFilesStoragePath = 'dont-use-this-one';
    }
}
