<?php

namespace App\Providers;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Policies\SubIdxBatchPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        SubIdxBatch::class => SubIdxBatchPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }

}
