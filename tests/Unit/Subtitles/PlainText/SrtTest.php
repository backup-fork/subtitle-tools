<?php

namespace Tests\Unit\Subtitles\PlainText;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\PlainText\SrtCue;
use App\Subtitles\ShiftsCues;
use Tests\TestCase;

class SrtTest extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/three-cues.srt');

        $this->assertSame('three-cues', $srt->getFileNameWithoutExtension());

        $cues = $srt->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(1266, $cues[0]->getStartMs());
        $this->assertSame(3366, $cues[0]->getEndMs());
        $this->assertSame(['Do you know what this is all', 'about? Why we\'re here?'], $cues[0]->getLines());

        $this->assertSame(3400, $cues[1]->getStartMs());
        $this->assertSame(6366, $cues[1]->getEndMs());
        $this->assertSame(['To be out. This is out.', '[AUDIENCE LAUGHS]'], $cues[1]->getLines());

        $this->assertSame(6400, $cues[2]->getStartMs());
        $this->assertSame(8233, $cues[2]->getEndMs());
        $this->assertSame(['And out is one of', 'the single most'], $cues[2]->getLines());
    }

    /** @test */
    function it_preserves_valid_srt_files()
    {
        $filePath = $this->testFilesStoragePath.'text/srt/three-cues.srt';

        $srt = new Srt($filePath);

        $content = read_lines($filePath);

        $this->assertSame($content, $srt->getContentLines());
    }

    /** @test */
    function it_returns_empty_content_if_there_are_no_cues()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/empty.srt');

        $this->assertSame('', $srt->getContent());

        $this->assertSame([], $srt->getContentLines());
    }

    /** @test */
    function it_parses_edge_cases()
    {
        // Starts with a timing line
        $srt = new Srt($this->testFilesStoragePath.'text/srt/parse-edge-case-1.srt');
        $cues = $srt->getCues();
        $this->assertEquals(5, count($cues));

        // Ends with a timing line, it isnt added because it has no text lines
        $srt = new Srt($this->testFilesStoragePath.'text/srt/parse-edge-case-2.srt');
        $cues = $srt->getCues();
        $this->assertEquals(5, count($cues));

        // Starts with three timing lines in a row, and some random timings without text
        $srt = new Srt($this->testFilesStoragePath.'text/srt/parse-edge-case-3.srt');
        $cues = $srt->getCues();
        $this->assertEquals(1, count($cues));
        $this->assertEquals($cues[0]->getLines()[0], 'One of them,');
        $this->assertEquals($cues[0]->getLines()[1], 'her total was $8.00.');
        $this->assertEquals(false, isset($cues[0]->getLines()[2]));

        // doesn't have a trailing empty line
        $srt = new Srt($this->testFilesStoragePath.'text/srt/parse-edge-case-4.srt');
        $cues = $srt->getCues();
        $this->assertEquals(5, count($cues));
        $this->assertEquals($cues[4]->getLines()[0], 'They both, of course,');
        $this->assertEquals($cues[4]->getLines()[1], 'choose to pay');
        $this->assertEquals(false, isset($cues[4]->getLines()[2]));

        // doesn't have a trailing empty line
        $srt = new Srt($this->testFilesStoragePath.'text/srt/parse-edge-case-5.srt');
        $cues = $srt->getCues();
        $this->assertEquals(5, count($cues));
        $this->assertEquals($cues[4]->getLines()[0], 'They both, of course,');
        $this->assertEquals(false, isset($cues[4]->getLines()[1]));
    }

    /** @test */
    function it_shifts_cues()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/three-cues.srt');

        $this->assertTrue($srt instanceof ShiftsCues);

        $this->assertSame(1266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(3366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(3400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(6366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(6400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(8233, $srt->getCues()[2]->getEndMs());

        $srt->shift(1000);

        $this->assertSame(2266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(4366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(4400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(7366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(7400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(9233, $srt->getCues()[2]->getEndMs());

        $srt->shift('-1000');

        $this->assertSame(1266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(3366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(3400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(6366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(6400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(8233, $srt->getCues()[2]->getEndMs());
    }

    /** @test */
    function it_partial_shifts_cues()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/three-cues.srt');

        $this->assertTrue($srt instanceof PartialShiftsCues);

        $this->assertSame(1266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(3366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(3400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(6366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(6400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(8233, $srt->getCues()[2]->getEndMs());

        $srt->shiftPartial(0, 3500, 1000);

        $this->assertSame(2266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(4366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(4400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(7366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(6400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(8233, $srt->getCues()[2]->getEndMs());

        $srt->shiftPartial(4400, 6500, '-1000');

        $this->assertSame(2266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(4366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(3400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(6366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(5400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(7233, $srt->getCues()[2]->getEndMs());
    }

    /** @test */
    function load_file_removes_empty_and_duplicate_cues()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/empty-and-duplicate-cues.srt');

        $this->assertTrue($srt instanceof ContainsGenericCues);

        $this->assertSame(1, count($srt->getCues()));

        $srt2 = new Srt();
        $srt2->loadFile("{$this->testFilesStoragePath}text/srt/empty-and-duplicate-cues.srt");

        $this->assertSame(1, count($srt2->getCues()));
    }

    /** @test */
    function load_file_does_not_watermark()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/empty.srt');

        $this->assertSame(0, count($srt->getCues()));
    }

    /** @test */
    function content_ends_with_empty_line()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/three-cues.srt');

        $this->assertTrue(ends_with($srt->getContent(), "\r\n"));
    }

    /** @test */
    function empty_file_content_is_empty()
    {
        $srt = new Srt();

        $this->assertSame('', $srt->getContent());
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/three-cues.srt');

        $generic = $srt->toGenericSubtitle();

        $this->assertFalse($generic instanceof Srt);

        $this->assertSame('three-cues', $generic->getFileNameWithoutExtension());

        $cues = $generic->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(1266, $cues[0]->getStartMs());
        $this->assertSame(3366, $cues[0]->getEndMs());
        $this->assertSame(['Do you know what this is all', 'about? Why we\'re here?'], $cues[0]->getLines());
        $this->assertFalse($cues[0] instanceof SrtCue);

        $this->assertSame(3400, $cues[1]->getStartMs());
        $this->assertSame(6366, $cues[1]->getEndMs());
        $this->assertSame(['To be out. This is out.', '[AUDIENCE LAUGHS]'], $cues[1]->getLines());
        $this->assertFalse($cues[1] instanceof SrtCue);

        $this->assertSame(6400, $cues[2]->getStartMs());
        $this->assertSame(8233, $cues[2]->getEndMs());
        $this->assertSame(['And out is one of', 'the single most'], $cues[2]->getLines());
        $this->assertFalse($cues[2] instanceof SrtCue);
    }

    /** @test */
    function it_parses_files_with_coordinates()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/srt-with-coordinates.srt');

        $cues = $srt->getCues();

        $this->assertCount(5, $cues);

        $this->assertSame([
            '00:00:06,012 --> 00:00:08,321',
            '- I\'m ready.',
            '- You sure?',
            '',
        ], $cues[0]->toArray());
    }

    /** @test */
    function it_parses_files_with_common_mistakes()
    {
        $srt = new Srt($this->testFilesStoragePath.'text/srt/broken-by-google-translate.srt');

        $cues = $srt->getCues();

        $this->assertCount(4, $cues);

        $this->assertSame([
            '00:02:50,399 --> 00:02:53,334',
            'Go there, take the weapon.',
            '',
        ], $cues[2]->toArray());
    }
}
