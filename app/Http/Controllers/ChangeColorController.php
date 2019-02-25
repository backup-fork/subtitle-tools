<?php

namespace App\Http\Controllers;

use App\Jobs\FileJobs\ChangeColorJob;
use App\Subtitles\Tools\Options\ChangeColorOptions;

class ChangeColorController extends FileJobController
{
    protected $indexRouteName = 'changeColor';

    protected $job = ChangeColorJob::class;

    protected $options = ChangeColorOptions::class;

    public function index()
    {
        return view('tools.change-color');
    }
}
