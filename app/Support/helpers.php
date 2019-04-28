<?php

use App\Models\StoredFile;
use App\Models\User;
use App\Support\TextFile\Facades\TextFileIdentifier;
use App\Support\TextFile\Facades\TextFileReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

const DETERMINISTIC = 'factory_deterministic';

/**
 * @param $file string|StoredFile|UploadedFile
 *
 * @return array
 */
function read_lines($file)
{
    if ($file instanceof StoredFile) {
        $file = $file->file_path;
    } elseif ($file instanceof UploadedFile) {
        $file = $file->getRealPath();
    }

    return TextFileReader::getLines($file);
}

/**
 * @param $file string|StoredFile|UploadedFile
 *
 * @return string
 */
function read_content($file)
{
    if ($file instanceof StoredFile) {
        $file = $file->file_path;
    } elseif ($file instanceof UploadedFile) {
        $file = $file->getRealPath();
    }

    return TextFileReader::getContent($file);
}

/**
 * @param $file string|StoredFile|UploadedFile
 *
 * @return bool
 */
function is_text_file($file)
{
    if ($file instanceof StoredFile) {
        $file = $file->file_path;
    } elseif ($file instanceof UploadedFile) {
        $file = $file->getRealPath();
    }

    return TextFileIdentifier::isTextFile($file);
}

function generate_url_key()
{
    return substr(sha1(str_random(16)), 0, 16);
}

function file_mime($filePath)
{
    if (! file_exists($filePath)) {
        throw new RuntimeException('File does not exist: '.$filePath);
    }

    try {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
    } catch (Exception $e) {
        return 'application/octet-stream';
    }

    return $mimeType;
}

function storage_disk_file_path($path, $disk = null)
{
    $disk = $disk ?: config('filesystems.default');

    $storagePath = realpath(Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix());

    return rtrim($storagePath, '/').'/'.ltrim($path, '/');
}

function format_file_size($bytes)
{
    $bytes = abs($bytes);

    $units = ['b', 'kb', 'mb', 'gb', 'tb'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, 0).' '.$units[$pow];
}

/**
 * Trigger a "dd()" after it has been called "timesCalled" times.
 *
 * @param $vars
 *
 * @param int $timesCalled
 */
function dd_delay(int $timesCalled, ...$vars)
{
    static $calls = [];

    $caller = sha1(debug_backtrace()[0]['file'].'|'.debug_backtrace()[0]['line']);

    $callCount = $calls[$caller] ?? 1;

    if ($callCount === $timesCalled) {
        dd($vars);
    }

    $calls[$caller] = $callCount + 1;
}

function file_hash(...$files)
{
    if (! $files) {
        throw new RuntimeException('"file_hash()" requires at least one argument');
    }

    $hashes = array_map(function ($file) {
        if ($file instanceof UploadedFile) {
            $file = $file->getRealPath();
        }

        return sha1_file($file);
    }, $files);

    return count($hashes) === 1 ? $hashes[0] : $hashes;
}

function user(): User
{
    $user = Auth::user();

    if (! $user) {
        throw new RuntimeException('Not logged in');
    }

    return $user;
}

function name_without_extension($file)
{
    if ($file instanceof UploadedFile) {
        $file = $file->getClientOriginalName();
    }

    return pathinfo($file, PATHINFO_FILENAME);
}
