<?php

namespace Tests\Unit\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PostsFileJobs;
use Tests\TestCase;

class MergeControllerTest extends TestCase
{
    use RefreshDatabase, PostsFileJobs;

    protected $snapshotDirectory = 'merge';

    /** @test */
    function it_can_simple_merge_subtitles_into_srt_files()
    {
        $this->snapshotSimpleMerge('text/srt/three-cues.srt', 'text/ass/three-cues.ass');
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_srt_files_with_color()
    {
        $this->snapshotSimpleMerge('text/srt/three-cues.srt', 'text/ass/three-cues.ass', [
            'should_color_base_subtitle' => true,
            'should_color_merge_subtitle' => true,
            'base_subtitle_color' => '#BBBBBB',
            'merge_subtitle_color' => '#AAAAAA',
        ]);
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_ass_files()
    {
        $this->snapshotSimpleMerge('text/ass/three-cues.ass', 'text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_ass_files_with_color()
    {
        $this->snapshotSimpleMerge('text/ass/three-cues.ass', 'text/srt/three-cues.srt', [
            'should_color_base_subtitle' => true,
            'should_color_merge_subtitle' => true,
            'base_subtitle_color' => '#BBBBBB',
            'merge_subtitle_color' => '#AAAAAA',
        ]);
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_ssa_files()
    {
        $this->snapshotSimpleMerge('text/ssa/three-cues.ssa', 'text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_vtt_files()
    {
        $this->snapshotSimpleMerge('text/vtt/three-cues.vtt', 'text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_nearest_cue_merge_subtitles_into_ass_files()
    {
        $this->snapshotNearestCueMerge('text/ass/merge-01.ass', 'text/srt/merge-01.srt', 3000);
    }

    /** @test */
    function it_can_nearest_cue_merge_two_srt_files()
    {
        $this->snapshotNearestCueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', 3000);
    }

    /** @test */
    function it_can_top_bottom_merge_subtitles_into_ass_files()
    {
        $this->snapshotTopBottomMerge('text/ass/three-cues.ass', 'text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_top_bottom_merge_subtitles_into_srt_files()
    {
        $this->snapshotTopBottomMerge('text/srt/three-cues.srt', 'text/ass/three-cues.ass');
    }

    /** @test */
    function it_can_glue_merge_if_the_base_subtitle_has_no_cues()
    {
        $this->snapshotGlueMerge('text/vtt/webvtt-no-dialogue.vtt', 'text/srt/three-cues.srt', 1000);
    }

    /** @test */
    function the_base_subtitle_has_to_be_of_a_supported_format()
    {
        $this->post(route('merge'), [
                'subtitles' => $this->createUploadedFile('text/smi/smi01.smi'),
                'second-subtitle' => $this->createUploadedFile('text/ass/three-cues.ass'),
                'mode' => 'simple',
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => 'The base subtitle format is not supported']);
    }

    /** @test */
    function it_can_glue_two_srt_files_end_to_end()
    {
        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', 0);

        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', 3000);

        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', -3000);

        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', -99999);
    }

    /** @test */
    function it_can_glue_merge_two_different_formats()
    {
        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/ass/three-cues.ass', 1000);

        $this->snapshotGlueMerge('text/ass/three-cues.ass', 'text/srt/three-cues.srt', 1000);

        $this->snapshotGlueMerge('text/ssa/three-cues.ssa', 'text/srt/three-cues.srt', 1000);

        $this->snapshotGlueMerge('text/vtt/three-cues.vtt', 'text/ass/three-cues.ass', 1000);
    }

    private function snapshotSimpleMerge($baseFile, $mergeFile, $attributes = [])
    {
        $this->snapshotMerge([
            'subtitles' => $this->createUploadedFile($baseFile),
            'second-subtitle' => $this->createUploadedFile($mergeFile),
            'mode' => 'simple',
        ] + $attributes);
    }

    private function snapshotTopBottomMerge($baseFile, $mergeFile)
    {
        $this->snapshotMerge([
            'subtitles' => $this->createUploadedFile($baseFile),
            'second-subtitle' => $this->createUploadedFile($mergeFile),
            'mode' => 'topBottom',
        ]);
    }

    private function snapshotNearestCueMerge($baseFile, $mergeFile, $threshold)
    {
        $this->snapshotMerge([
            'subtitles' => $this->createUploadedFile($baseFile),
            'second-subtitle' => $this->createUploadedFile($mergeFile),
            'nearest_cue_threshold' => $threshold,
            'mode' => 'nearestCueThreshold',
        ]);
    }

    private function snapshotGlueMerge($baseFile, $mergeFile, $offset)
    {
        $this->snapshotMerge([
            'subtitles' => $this->createUploadedFile($baseFile),
            'second-subtitle' => $this->createUploadedFile($mergeFile),
            'glue_offset' => $offset,
            'mode' => 'glue',
        ]);
    }

    private function snapshotMerge($attributes)
    {
        [$response, $fileGroup] = $this->postFileJob('merge', [], $attributes + [
            'nearest_cue_threshold' => 1000,
            'glue_offset' => 1000,
            'mode' => 'simple',
            'should_color_base_subtitle' => false,
            'should_color_merge_subtitle' => false,
            'base_subtitle_color' => '#FFFFFF',
            'merge_subtitle_color' => '#FFFFFF',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(
            $fileGroup->fileJobs->first()->output_stored_file_id
        );
    }
}
