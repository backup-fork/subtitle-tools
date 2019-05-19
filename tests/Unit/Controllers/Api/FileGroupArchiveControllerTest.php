<?php

namespace Tests\Unit\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileGroupArchiveControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_archive_status()
    {
        $fileGroup = $this->createFileGroup();

        $this->showArchiveStatus($fileGroup)
            ->assertStatus(200)
            ->assertExactJson([
                'archiveDownloadUrl' => false,
                'archiveRequestUrl' => route('api.fileGroupArchive.request', $fileGroup->url_key),
                'archiveStatus' => 'Request zip file',
            ]);
    }

    /** @test */
    function it_can_request_an_archive()
    {
        $fileGroup = $this->createFileGroup(['file_jobs_finished_at' => now(), 'archive_requested_at' => null]);

        $this->requestArchive($fileGroup)
            ->assertStatus(200);

        $fileGroup->refresh();

        $this->assertNotNull($fileGroup->archive_requested_at);

        $this->assertSame('application/zip', file_mime($fileGroup->archiveStoredFile->file_path));
    }

    /** @test */
    function it_wont_request_an_archive_when_the_file_group_is_not_done()
    {
        $fileGroup = $this->createFileGroup(['file_jobs_finished_at' => null, 'archive_requested_at' => null]);

        $this->requestArchive($fileGroup)
            ->assertStatus(404);
    }

    /** @test */
    function it_wont_request_an_archive_if_one_was_already_requested()
    {
        $fileGroup = $this->createFileGroup(['file_jobs_finished_at' => now(), 'archive_requested_at' => now()]);

        $this->requestArchive($fileGroup)
            ->assertStatus(404);
    }

    private function showArchiveStatus($fileGroup)
    {
        return $this->getJson(route('api.fileGroupArchive.show', $fileGroup->url_key));
    }

    private function requestArchive($fileGroup)
    {
        return $this->postJson(route('api.fileGroupArchive.request', $fileGroup->url_key));
    }
}
