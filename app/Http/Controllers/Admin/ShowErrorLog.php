<?php

namespace App\Http\Controllers\Admin;

class ShowErrorLog
{
    public function __invoke()
    {
        $filePath = storage_path('logs/laravel.log');

        $content = file_exists($filePath) ? read_content($filePath) : '';

        return '<pre>'.$content.'</pre>';
    }
}
