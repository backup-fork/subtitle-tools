<?php

namespace App\Http\Controllers\Admin;

use App\Models\DiskUsage;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function index()
    {
        $feedbackFilePath = storage_path('logs/feedback.log');
        $logFilePath = storage_path('logs/laravel.log');

        return view('admin.dashboard', [
            'feedbackLines' => file_exists($feedbackFilePath) ? read_lines($feedbackFilePath) : [],
            'errorLogLines' => file_exists($logFilePath) ? read_lines($logFilePath) : [],
            'supervisor' => $this->getSupervisorInfo(),
            'diskUsage' => DiskUsage::latest()->first() ?? optional(),
            'dependencies' => $this->getDependenciesInfo(),
            'failedJobs' => DB::table('failed_jobs')->get(),
        ]);
    }

    private function getSupervisorInfo()
    {
        $lines = app()->environment('local') ? [
            'st-worker-broadcast:st-worker-broadcast_00   RUNNING   pid 27243, uptime 0:04:20',
            'st-worker-default:st-worker-default_00       RUNNING   pid 27244, uptime 0:13:37',
            'st-worker-subidx:st-worker-subidx_00         RUNNING   pid 27245, uptime 2:22:22',
            'st-worker-larry:st-worker-larry_00           RUNNING   pid 27246, uptime 2:22:22',
        ] : explode("\n", shell_exec('supervisorctl status'));

        return collect($lines)->filter(function ($line) {
            return ! empty($line);
        })->map(function ($line) {
           return preg_split('/ {3,}|, /', $line);
        })->map(function ($parts) {
            return (object)[
                'worker'    => str_before($parts[0], ':'),
                'name'      => str_after($parts[0], ':'),
                'status'    => strtolower($parts[1] ?? 'UNKNOWN'),
                'isRunning' => $parts[1] ?? 'UNKNOWN' === 'RUNNING',
                'pid'       => str_after($parts[2] ?? '?', 'pid '),
                'uptime'    => str_after($parts[3] ?? '?:??:??', 'uptime '),
            ];
        })->filter(function ($object) {
            return starts_with($object->worker, 'st-');
        });
    }

    private function getDependenciesInfo()
    {
        $dependencies = [];

        $postMaxSize = ini_get('post_max_size');
        $uploadMaxFileSize = ini_get('upload_max_filesize');
        $maxFileUploads = ini_get('max_file_uploads');

        $dependencies['post_max_size: '.$postMaxSize]             = (int) $postMaxSize >= 130;
        $dependencies['upload_max_filesize: '.$uploadMaxFileSize] = (int) $uploadMaxFileSize >= 130;
        $dependencies['max_file_uploads: '.$maxFileUploads]       = (int) $maxFileUploads >= 130;

        $dependencies['Opcache'] = function_exists('opcache_get_status') && (opcache_get_status()['opcache_enabled'] ?? false);

        $dependencies['Multibyte support'] = extension_loaded('mbstring');

        $dependencies['Zip archives'] = class_exists(\ZipArchive::class);

        // $dependencies['Rar archives (PECL)'] = class_exists(\RarArchive::class);

        $dependencies['Curl extension'] = function_exists('curl_exec');

        $dependencies['PHP GD (image library)'] = function_exists('imagecreatetruecolor');

        $dependencies['uchardet binary'] = ! empty(shell_exec('command -v uchardet'));

        $dependencies['Vobsub2srt binary'] = ! empty(shell_exec('command -v vobsub2srt'));

        $dependencies['Redis'] = ! empty(shell_exec('command -v redis-cli'));

        $dependencies['Tesseract binary'] = ! empty(shell_exec('command -v tesseract'));

        $dependencies['Tesseract traineddata'] = file_exists('/usr/share/tesseract-ocr/tessdata/nld.traineddata') || file_exists('/usr/local/share/tessdata/nld.traineddata');

        return collect($dependencies);
    }
}
