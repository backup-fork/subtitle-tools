<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue;

    public $tries = 1;

    public $timeout = 60;

    abstract public function handle();
}
