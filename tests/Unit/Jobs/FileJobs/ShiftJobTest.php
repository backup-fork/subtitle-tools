<?php

namespace Tests\Unit\Jobs\FileJobs;

use App\Jobs\FileJobs\ShiftJob;
use App\Models\FileGroup;
use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\Srt;
use App\Support\Facades\TextFileFormat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftJobTest extends TestCase
{
    use RefreshDatabase;
//
//    /** @test */
//    function it_shifts_a_subtitle_file()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        $fileGroup->job_options = [
//            'milliseconds' => 1000,
//        ];
//
//        $fileGroup->save();
//
//        dispatch(
//            new ShiftJob($fileGroup, $this->testFilesStoragePath.'text/srt/three-cues.srt')
//        );
//
//        $fileJob = $fileGroup->fileJobs()->firstOrFail();
//
//        $this->assertSame('three-cues.srt', $fileJob->original_name);
//
//        $convertedStoredFile = $fileJob->outputStoredFile;
//
//        /** @var $subtitle ContainsGenericCues */
//        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);
//
//        $this->assertTrue($subtitle instanceof Srt);
//
//        $cues = $subtitle->getCues();
//
//        $this->assertSame(3, count($cues));
//
//        $this->assertSame(2266, $cues[0]->getStartMs());
//        $this->assertSame(4366, $cues[0]->getEndMs());
//
//        $this->assertSame(4400, $cues[1]->getStartMs());
//        $this->assertSame(7366, $cues[1]->getEndMs());
//    }
//
//    /** @test */
//    function it_rejects_text_files_that_are_not_shiftable()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        $fileGroup->job_options = [
//            'milliseconds' => 1000,
//        ];
//
//        $fileGroup->save();
//
//        dispatch(
//            new ShiftJob($fileGroup, $this->testFilesStoragePath.'text/normal01.txt')
//        );
//
//        $fileJob = $fileGroup->fileJobs()->firstOrFail();
//
//        $this->assertSame('messages.file_can_not_be_shifted', $fileJob->error_message);
//
//        $this->assertNull($fileJob->output_stored_file_id);
//    }
//
//    /** @test */
//    function it_rejects_files_that_are_not_text_files()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        $fileGroup->job_options = [
//            'milliseconds' => 1000,
//        ];
//
//        $fileGroup->save();
//
//        dispatch(
//            new ShiftJob($fileGroup, $this->testFilesStoragePath.'text/fake/dat.ass')
//        );
//
//        $fileJob = $fileGroup->fileJobs()->firstOrFail();
//
//        $this->assertSame('messages.not_a_text_file', $fileJob->error_message);
//
//        $this->assertNull($fileJob->output_stored_file_id);
//    }
//
//    /**
//     * @param string $toolRoute
//     * @param null $urlKey
//     *
//     * @return FileGroup
//     *
//     * @deprecated This is old, should be replaced by "createFileGroup" method from the "CreatesModels" trait
//     */
//    public function createFileGroup($toolRoute = 'default-route', $urlKey = null): FileGroup
//    {
//        $fileGroup = new FileGroup();
//
//        $fileGroup->fill([
//            'tool_route' => $toolRoute,
//            'url_key' => $urlKey ?? generate_url_key(),
//        ])->save();
//
//        return $fileGroup;
//    }
}
