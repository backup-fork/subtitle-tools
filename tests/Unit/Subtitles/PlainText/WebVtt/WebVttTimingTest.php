<?php

namespace Tests\Unit\Subtitles\PlainText\WebVtt;

use App\Subtitles\PlainText\WebVtt\WebVttTiming;
use Tests\TestCase;

class WebVttTimingTest extends TestCase
{
    /** @test */
    function it_can_parse_a_timing_without_style()
    {
        $timing = new WebVttTiming($original = '00:00:30.739 --> 00:00:34.074');

        $this->assertSame($original, $timing->toString());
    }

    /** @test */
    function it_can_parse_a_timing_with_style()
    {
        $timing = new WebVttTiming($original = '00:00:05.000 --> 00:00:10.000 line:0 position:20% size:60% align:start');

        $this->assertSame($original, $timing->toString());
    }
}
