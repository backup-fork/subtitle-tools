<?php

namespace App\Providers;

use App\Events\SubIdxBatchFinished;
use App\Listeners\SubIdxBatch\RemoveSourceFiles;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubIdxBatchFinished::class => [
            RemoveSourceFiles::class,
        ],
    ];
}
