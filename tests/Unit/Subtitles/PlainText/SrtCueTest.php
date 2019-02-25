<?php

namespace Tests\Unit\Subtitles\PlainText;

use App\Subtitles\PlainText\SrtCue;
use Illuminate\Support\Arr;
use Tests\TestCase;

class SrtCueTest extends TestCase
{
    /** @test */
    function it_identifies_valid_timing_strings()
    {
        $this->assertValidTimingString([
            '00:00:00,000 --> 00:00:00,000',
            "99:59:59,999 --> 99:59:59,999\r\n",
            '00:00:28,400 --> 00:00:29,533 ',
            ' 00:13:25,707 --> 00:13:27,507',

            '00: 07: 39.053 --> 00: 07: 43.683  X1: 112 X2: 602 Y1: 444 Y2: 523',
            '00:02:26,407 --> 00:02:31,356 X1:100 X2:100 Y1:100 Y2:100',
            '00:02:26,407 --> 00:02:31,356  X1:100 X2:100 Y1:100 Y2:100',
            '00:00:36,452 --> 00:00:38,920  x1:205 x2:512 y1:450 y2:524',

            // Dot instead of comma as milliseconds separator
            '00:00:00.000 --> 00:00:00.000',
            '00:00:00,000 --> 00:00:00.000',
            '00:00:05.123 --> 00:12:01,100',

            // Spaces after the colons, or a single dash error. (I think Google Translate causes this)
            '00: 00: 05,123 --> 00: 12: 01,100',
            '00:00:05,123 --> 00:12: 01,100',
            '00:00:00,400 -> 00:03:29,533',

            // one digit for hours, two for milliseconds
            '0:00:01,26 --> 0:00:03,36',
        ]);
    }

    /** @test */
    function it_rejects_invalid_timing_strings()
    {
        $this->assertInvalidTimingString([
            '00:13:25,707 --> 00:13:27,5',
            '100:00:01,266 --> 125:00:03,366',
            'This man is out of ideas.',
            '',

            // Another popular subtitle shifter can (incorrectly) turn timecodes negative
            '-1:59:40,266 --> -1:59:42,366',
            '01:59:-0,266 --> 01:59:02,366',
            '01:-9:40,266 --> 01:59:42,366',
            '-1:59:57,100 --> 00:00:00,366',

            // This cue has the arrow after the letter 'x', this caused problems
            'Dialogue: 0,0:06:41.75,0:06:43.54,sign_9633_65_Win____earn_two,Text,0000,0000,0000,,Win -> Earn Two',

            // Ends before it starts
            '00:00:00,001 --> 00:00:00,000',

            // This cue ends before it begins, after fixing the two digit ms timestamp
            '00:07:42,87 --> 00:07:42,796'
        ]);
    }

    /** @test */
    function it_makes_timing_strings()
    {
        $this->assertSame(
            '00:00:00,000 --> 00:00:01,234',
            (new SrtCue)->setTiming(0, 1234)->getTimingString()
        );

        $this->assertParsedTiming([
            '00:00:00,000 --> 00:00:00,000' => '00:00:00,000 --> 00:00:00,000',
            '00:01:01,266 --> 00:01:03,366' => '00:01:01,266 --> 00:01:03,366',
            '12:34:56,789 --> 21:00:29,533' => '12:34:56,789 --> 21:00:29,533',

            // Two digits for ms
            '00:00:01,26 --> 00:00:03,36' => '00:00:01,260 --> 00:00:03,360',

            // spaces after colons
            '00: 00: 05,123 --> 00: 12: 01,100' => '00:00:05,123 --> 00:12:01,100',

            // dot instead of comma
            '00:00:00.000 --> 00:00:00.000' => '00:00:00,000 --> 00:00:00,000',

            // single dash arrow
            '00:00:00,400 -> 00:03:29,533' => '00:00:00,400 --> 00:03:29,533',
        ]);
    }

    /** @test */
    function it_does_not_preserve_coordinates()
    {
        $this->assertSame(
            '00:13:37,413 --> 00:13:41,167',
            (new SrtCue)->setTimingFromString('00:13:37,413 --> 00:13:41,167  X1:183 X2:533 Y1:444 Y2:523')->getTimingString()
        );
    }

    /** @test */
    function timecodes_do_not_exceed_minimum_and_maximum_values()
    {
        $this->assertSame(
            '99:59:59,999 --> 99:59:59,999',
            (new SrtCue)->shift('9999999999999999999999999')->getTimingString()
        );

        $this->assertSame(
            '00:00:00,000 --> 00:00:00,000',
            (new SrtCue)->shift('-9999999999999999999999999')->getTimingString()
        );
    }

    /** @test */
    function it_converts_to_array()
    {
        $cue = (new SrtCue)
            ->setTimingFromString('00:01:01,266 --> 00:01:03,366')
            ->addLine('First line')
            ->addLine('Second line!');

        $this->assertSame([
            '00:01:01,266 --> 00:01:03,366',
            'First line',
            'Second line!',
            '',
        ], $cue->toArray());
    }

    /** @test */
    function it_trims_timing_lines()
    {
        // regression test for a timing line that had a tab at the end.
        // ::isTimingString trimmed the line, but setTimingFromString didn't
        $this->assertValidTimingString([
            "\t00:01:01,266 --> 00:01:03,366",
            "00:01:01,266 --> 00:01:03,366\t",
        ]);

        // Assert no exceptions are thrown
        (new SrtCue)->setTimingFromString("\t00:01:01,266 --> 00:01:03,366");
        (new SrtCue)->setTimingFromString("00:01:01,266 --> 00:01:03,366\t");
    }

    /** @test */
    function it_can_be_styled_to_show_on_top()
    {
        $cue = (new SrtCue)
            ->addLine('Wow!')
            ->stylePositionTop();

        $this->assertSame(['{\an8}Wow!'], $cue->getLines());

        // It should not apply the style twice.
        $cue = (new SrtCue)
            ->addLine('{\an8}Wow!')
            ->stylePositionTop();

        $this->assertSame(['{\an8}Wow!'], $cue->getLines());
    }

    /** @test */
    function it_can_color_lines()
    {
        $cue1 = (new SrtCue)
            ->addLine('Wow!')
            ->changeColor('#4286f4');
        $this->assertSame(['<font color="#4286f4">Wow!</font>'], $cue1->getLines());

        $cue2 = (new SrtCue)
            ->addLine('First line')
            ->addLine('Second line!')
            ->changeColor('#e4e4e4');
        $this->assertSame(['<font color="#e4e4e4">First line', 'Second line!</font>'], $cue2->getLines());
    }

    private function assertValidTimingString($timingStrings)
    {
        foreach (Arr::wrap($timingStrings) as $timingString) {
            $isValid = SrtCue::isTimingString($timingString);

            $this->assertTrue($isValid, 'Unexpected invalid timing string: '.$timingString);
        }
    }

    private function assertInvalidTimingString($timingStrings)
    {
        foreach (Arr::wrap($timingStrings) as $timingString) {
            $isValid = SrtCue::isTimingString($timingString);

            $this->assertFalse($isValid, 'Unexpected valid timing string: ' . $timingString);
        }
    }

    private function assertParsedTiming($expected, $input = null)
    {
        if (! is_array($expected)) {
            $expected = [$input => $expected];
        }

        foreach ($expected as $input => $expectedTiming) {
            $this->assertSame(
                $expectedTiming,
                (new SrtCue)->setTimingFromString($input)->getTimingString()
            );
        }
    }
}
