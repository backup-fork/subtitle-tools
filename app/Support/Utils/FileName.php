<?php

namespace App\Support\Utils;

class FileName
{
    public function getExtension($fileName, $toLower = true)
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        return $toLower ? strtolower($extension) : $extension;
    }

    public function changeExtension($fileName, $newExtension)
    {
        $newExtension = trim($newExtension, '. ');

        if (empty($newExtension)) {
            return $this->getWithoutExtension($fileName);
        }

        return $this->getWithoutExtension($fileName) . '.' . $newExtension;
    }

    public function getWithoutExtension($fileName)
    {
        $name = substr($fileName, 0, strlen($fileName) - strlen($this->getExtension($fileName)));

        return rtrim($name, '. ');
    }

    public function appendName($fileName, $append = '-st')
    {
        $extension = $this->getExtension($fileName);

        $withoutExtension = $this->getWithoutExtension($fileName);

        return rtrim($withoutExtension . $append . '.' . $extension, ' .');
    }

    public function watermark($fileName)
    {
        if (stripos($fileName, 'subtitletools') !== false) {
            return $fileName;
        }

        return $this->appendName($fileName, ' [SubtitleTools.com]');
    }
}
