<?php

namespace App\Jobs;

use App\Models\FileGroup;
use App\Models\FileJob;
use App\Support\Archive\StoredFileArchive;
use Illuminate\Contracts\Queue\ShouldQueue;

class ZipFileGroupJob extends BaseJob implements ShouldQueue
{
    public $timeout = 120;

    public $fileGroup;

    public function __construct(FileGroup $fileGroup)
    {
        $this->fileGroup = $fileGroup;
    }

    public function handle()
    {
        $archive = new StoredFileArchive();

        $this->fileGroup
            ->fileJobs
            ->filter(function (FileJob $fileJob) {
                return $fileJob->output_stored_file_id !== null;
            })
            ->each(function (FileJob $fileJob) use ($archive) {
                $archive->add($fileJob->outputStoredFile, $fileJob->original_name_with_new_extension);
            });

        $this->fileGroup->update([
            'archive_stored_file_id' => $archive->store()->id,
            'archive_finished_at' => now(),
        ]);
    }

    public function failed($exception)
    {
        $this->fileGroup->update([
            'archive_error' => 'messages.zip-job.unknown_error',
            'archive_finished_at' => now(),
        ]);
    }
}
