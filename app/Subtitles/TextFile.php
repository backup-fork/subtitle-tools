<?php

namespace App\Subtitles;

use Illuminate\Http\UploadedFile;

abstract class TextFile
{
    protected $originalFileNameWithoutExtension = 'default';

    protected $extension = 'txt';

    protected $filePath = false;

    public function __construct($source = null)
    {
        if ($source === null) {
            return;
        } elseif ($this instanceof LoadsGenericSubtitles && $source instanceof TransformsToGenericSubtitle) {
            $this->loadGenericSubtitle($source->toGenericSubtitle());
        } else {
            $this->loadFile($source);
        }
    }

    /**
     * Returns true if the $filePath file is a valid format for this class.
     *
     * @param $file
     *
     * @return bool
     */
    abstract public static function isThisFormat($file);

    abstract public function getContent();

    abstract public function getContentLines();

    public function getExtension()
    {
        return $this->extension;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFileNameWithoutExtension($fileNameWithoutExtension)
    {
        $this->originalFileNameWithoutExtension = $fileNameWithoutExtension;

        return $this;
    }

    public function getFileNameWithoutExtension()
    {
        return $this->originalFileNameWithoutExtension;
    }

    public function loadFile($file)
    {
        $this->originalFileNameWithoutExtension = name_without_extension($file);

        $this->filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        // These properties come from the WithFileContent/WithFileLines trait
        if (property_exists($this, 'lines')) {
            $this->lines = array_map('trim', read_lines($this->filePath));
        } else {
            $this->content = read_content($this->filePath);
        }

        return $this;
    }

    public function loadFileFromFormat($file, $sourceFormat)
    {
        return $this->loadFile($file);
    }
}
