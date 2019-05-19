<?php

namespace App\Support\Archive;

use App\Models\StoredFile;
use App\Support\Utils\FileName;
use App\Support\Utils\TempFile;
use RuntimeException;
use ZipArchive;

class StoredFileArchive
{
    private $files = [];

    public function add(StoredFile $storedFile, $originalName)
    {
        $this->files[] = [$storedFile, $originalName];

        return $this;
    }

    public function store(): StoredFile
    {
        if (! $this->files) {
            throw new RuntimeException('No files added to archive');
        }

        $zip = new ZipArchive();

        $tempFilePath = (new TempFile)->makeFilePath('zip');

        $fileNameUtil = new FileName();

        if ($zip->open($tempFilePath, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Failed to open archive');
        }

        $alreadyAddedNames = [];

        /** @var $storedFile StoredFile */
        foreach ($this->files as [$storedFile, $nameInZip]) {
            while (in_array(strtolower($nameInZip), $alreadyAddedNames)) {
                $nameInZip = $fileNameUtil->appendName($nameInZip, '-st');
            }

            $alreadyAddedNames[] = strtolower($nameInZip);

            $zip->addFile($storedFile->file_path, $nameInZip);
        }

        if ($zip->close() !== true) {
            throw new RuntimeException('Failed to close archive');
        }

        $storedFileZip = StoredFile::getOrCreate($tempFilePath);

        unlink($tempFilePath);

        return $storedFileZip;
    }
}
