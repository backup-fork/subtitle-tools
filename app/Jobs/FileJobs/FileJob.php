<?php

namespace App\Jobs\FileJobs;

use App\Exceptions\FileJobException;
use App\Jobs\BaseJob;
use App\Subtitles\TextFile;
use App\Subtitles\TextFileFormat;
use App\Subtitles\Tools\Options\NoOptions;
use App\Support\Facades\TempFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\FileJobChanged;
use App\Models\FileJob as FileJobModel;
use App\Models\StoredFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;

abstract class FileJob extends BaseJob implements ShouldQueue
{
    protected $fileGroup;

    protected $inputStoredFile;

    protected $fileJob;

    public function __construct(FileJobModel $fileJobModel)
    {
        $this->fileJob = $fileJobModel;

        $this->fileGroup = $fileJobModel->fileGroup;

        $this->inputStoredFile = $fileJobModel->inputStoredFile;
    }

    public function handle()
    {
        $this->fileJob->started_at = now();

        $options = $this->createOptions($this->fileGroup->job_options);

        if (! is_text_file($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $subtitle = (new TextFileFormat)->get($this->inputStoredFile->filePath);

        try {
            $subtitle = $this->process($subtitle, $options);
        } catch (FileJobException $e) {
            return $this->abortFileJob($e->getMessage());
        }

        $outputStoredFile = StoredFile::createFromTextFile($subtitle);

        return $this->finishFileJob($outputStoredFile);
    }

    public function process(TextFile $subtitle, $options)
    {
        throw new RuntimeException('Implement this, or override "->handle()"');
    }

    public function startFileJob()
    {
        $this->fileJob->started_at = now();
    }

    public function finishFileJob(StoredFile $outputStoredFile)
    {
        $this->fileJob->fill([
            'output_stored_file_id' => $outputStoredFile->id,
            'new_extension' => $this->getNewExtension(),
            'finished_at' => now(),
        ]);

        return $this->endFileJob();
    }

    public function abortFileJob(string $errorMessage)
    {
        $this->fileJob->fill([
            'error_message' => $errorMessage,
            'finished_at' => now(),
        ]);

        return $this->endFileJob();
    }

    private function endFileJob()
    {
        $this->fileJob->save();

        $unfinishedJobsCount = $this->fileJob
            ->fileGroup
            ->fileJobs()
            ->whereNull('finished_at')
            ->count();

        if ($unfinishedJobsCount === 0) {
            $this->fileGroup->update(['file_jobs_finished_at' => now()]);
        }

        FileJobChanged::dispatch($this->fileJob);

        TempFile::cleanUp();

        return $this->fileJob;
    }

    protected function createOptions($data)
    {
        if (isset($this->options)) {
            return new $this->options($data);
        }

        $baseName = substr(class_basename(static::class), 0, -3);

        $optionsClass = '\\App\\Subtitles\\Tools\\Options\\'.$baseName.'Options';

        if (! class_exists($optionsClass)) {
            $optionsClass = NoOptions::class;
        }

        return new $optionsClass($data);
    }

    protected function abort($message): void
    {
        throw new FileJobException($message);
    }

    public function failed()
    {
        $this->abortFileJob('messages.unknown_error');

        Log::error("FileJob id: {$this->fileJob->id} (StoredFile id: {$this->fileJob->input_stored_file_id}) failed! (usually because of a TextEncodingException)");
    }

    public function getNewExtension()
    {
        $newExtension = $this->newExtension ?? null;

        if (! $newExtension) {
            throw new RuntimeException('New extension not set');
        }

        return $newExtension;
    }
}
