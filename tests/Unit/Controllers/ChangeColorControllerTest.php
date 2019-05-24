<?php

namespace Tests\Unit\Controllers;

use App\Models\FileJob;
use App\Subtitles\Tools\Options\ChangeColorOptions;
use App\Subtitles\Tools\Options\ToolOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeColorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $snapshotDirectory = 'change-color';

    /** @test */
    function it_can_color_an_srt_file()
    {
        $options = (new ChangeColorOptions)->setColor('#ff0000');

        $this->postChangeColor($options, 'text/srt/three-cues.srt')
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $fileJob = FileJob::firstOrFail();

        $this->assertSame('srt', $fileJob->new_extension);

        $this->assertMatchesFileSnapshot(
            $fileJob->outputStoredFile
        );
    }

    /** @test */
    function it_can_color_a_webvtt_file()
    {
        $options = (new ChangeColorOptions)->setColor('#e4e4e4');

        $this->postChangeColor($options, 'text/vtt/three-cues.vtt')
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $fileJob = FileJob::firstOrFail();

        $this->assertSame('vtt', $fileJob->new_extension);

        $this->assertMatchesFileSnapshot(
            $fileJob->outputStoredFile
        );
    }

    /** @test */
    function it_can_color_an_ass_file()
    {
        $options = (new ChangeColorOptions)->setColor('#aabbcc');

        $this->postChangeColor($options, 'text/ass/three-cues.ass')
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $fileJob = FileJob::firstOrFail();

        $this->assertSame('ass', $fileJob->new_extension);

        $this->assertMatchesFileSnapshot(
            $fileJob->outputStoredFile
        );
    }

    /** @test */
    function it_can_color_a_ssa_file()
    {
        $options = (new ChangeColorOptions)->setColor('#aabbcc');

        $this->postChangeColor($options, 'text/ssa/three-cues.ssa')
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $fileJob = FileJob::firstOrFail();

        $this->assertSame('ssa', $fileJob->new_extension);

        $this->assertMatchesFileSnapshot(
            $fileJob->outputStoredFile
        );
    }

    /** @test */
    function it_handles_files_that_cant_have_their_color_changed()
    {
        $options = (new ChangeColorOptions)->setColor('#ff0000');

        $this->postChangeColor($options, 'text/smi/three-cues.smi')
            ->assertSessionHasErrors('subtitles')
            ->assertStatus(302);

        $fileJob = FileJob::firstOrFail();

        $this->assertNull($fileJob->output_stored_file_id);

        $this->assertNotNull($fileJob->finished_at);
    }

    private function postChangeColor($options, $files)
    {
        $files = collect($files)->map(function ($path) {
            return $this->createUploadedFile($path);
        })->toArray();

        $options = $options instanceof ToolOptions ? $options->toArray() : $options;

        return $this->post(
            route('changeColor.post'),
            $options + ['subtitles' => $files]
        );
    }
}
