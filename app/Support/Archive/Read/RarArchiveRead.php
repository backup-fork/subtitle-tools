<?php

namespace App\Support\Archive\Read;

use App\Support\Archive\CompressedFile;
use Exception;
use RarArchive;

class RarArchiveRead extends ArchiveRead
{
    protected static $archiveMimeType = 'application/x-rar';

    protected $rar;

    public function __construct($filePath)
    {
        $this->rar = @RarArchive::open($filePath);

        if ($this->rar !== false) {
            $this->successfullyOpened = @$this->rar->isBroken() === false;
        }
    }

    /**
     * Return the amount of entries in this archive. Directories count as an entry.
     *
     * @return int
     */
    public function getEntriesCount()
    {
        return count($this->rar->getEntries());
    }

    /**
     * @return CompressedFile[]
     */
    public function getCompressedFiles()
    {
        $compressedFiles = [];

        $entries = $this->rar->getEntries();

        for ($i = 0; $i < count($entries); $i++) {
            if ($entries[$i]->isDirectory() || $entries[$i]->isEncrypted()) {
                continue;
            }

            $compressedFiles[] = new CompressedFile(
                $i,
                $entries[$i]->getName(),
                $entries[$i]->getUnpackedSize()
            );
        }

        return $compressedFiles;
    }

    protected function extract(CompressedFile $file, $destinationDirectory, $outputFileName)
    {
        $destinationFilePath = $destinationDirectory.$outputFileName;

        $rarEntry = $this->rar->getEntries()[$file->getIndex()];

        try {
            $rarEntry->extract(false, $destinationFilePath);
        } catch (Exception $e) {
            if ($e->getMessage() !== 'RarEntry::extract(): ERAR_BAD_DATA') {
                throw $e;
            }

            touch($destinationFilePath);
        }
    }

    public static function isAvailable()
    {
        return class_exists('\RarArchive');
    }
}
