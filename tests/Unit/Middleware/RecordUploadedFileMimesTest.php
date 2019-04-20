<?php

namespace Tests\Unit\Middleware;

use App\Models\UploadedFileMime;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecordUploadedFileMimesTest extends TestCase
{
    use RefreshDatabase;

    private $uri = '/convert-to-srt-online';

    /** @test */
    function it_records_file_mimes()
    {
        $this->postFiles([
                'text/normal01.txt',
                'text/srt/three-cues.srt',
                'text/srt/three-cues-chinese.srt',

                'sub-idx/error-and-nl.sub',

                'text/fake/dat.ass',
            ]);

        $this->assertRecordedMime('text/plain', 2);
        $this->assertRecordedMime('text/html', 1);
        $this->assertRecordedMime('image/x-tga', 1);
        $this->assertRecordedMime('application/x-innosetup', 1);
    }

    /** @test */
    function it_increments_existing_recorded_mimes()
    {
        UploadedFileMime::create(['uri' => $this->uri, 'mime' => 'text/plain', 'count' => 2]);
        UploadedFileMime::create(['uri' => '/another/uri', 'mime' => 'text/plain', 'count' => 50]);
        UploadedFileMime::create(['uri' => $this->uri, 'mime' => 'text/html', 'count' => 1]);

        $this->postFiles([
            'text/normal01.txt',
            'text/srt/three-cues.srt',
            'text/srt/three-cues-chinese.srt',

            'sub-idx/error-and-nl.sub',
        ]);

        $this->assertRecordedMime('text/plain', 4);
        $this->assertRecordedMime('text/html', 2);

        $this->assertRecordedMime('image/x-tga', 1);
    }

    /** @test */
    function it_records_archive_mimes_before_they_are_extracted()
    {
        $this->postFiles(['archives/zip/5-text-files-4-good.zip']);

        $this->assertRecordedMime('application/zip', 1);

        $this->assertSame(1, UploadedFileMime::count());
    }

    /** @test */
    function it_excludes_query_string_from_the_uri()
    {
        $this->postFiles(
            ['text/normal01.txt'],
            route('convertToSrt').'?wow=yes&a=1'
        );

        $this->assertRecordedMime('text/html', 1);
    }

    private function postFiles($files, $url = null)
    {
        $files = array_map(function ($value) {
            return is_string($value) ? $this->createUploadedFile($value) : $value;
        }, $files);

        return $this->post($url ?? route('convertToSrt'), ['subtitles' => $files])->assertStatus(302);
    }

    private function assertRecordedMime($mime, $count, $uri = null)
    {
        $uri = $uri ?: $this->uri;

        $exists = UploadedFileMime::query()
            ->where('mime', $mime)
            ->where('count', $count)
            ->where('uri', $uri)
            ->exists();

        if (! $exists) {
            $format = 'uri: %s, mime: %s, count: %s';

            $message = [
                'Could not find recorded file mime',
                '',
                sprintf($format, $uri, $mime, $count),
                '',
                'Database had:',
                '',
            ];

            foreach (UploadedFileMime::all() as $model) {
                $message[] = sprintf($format, $model->uri, $model->mime, $model->count);
            }

            $message[] = '';
        }

        $this->assertTrue($exists, implode("\n", $message ?? []));
    }
}
