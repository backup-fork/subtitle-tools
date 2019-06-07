<?php

namespace App\Providers;

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\RecordUserActivity;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function map()
    {
        Route::middleware('web')
            ->group(base_path('routes/web-guest-routes.php'));


        Route::middleware('api')
            ->name('api.')
            ->prefix('api/v1/')
            ->group(base_path('routes/api-guest-routes.php'));


        Route::middleware(['api', 'auth:api'])
            ->name('api.user.')
            ->prefix('api/v1/')
            ->group(base_path('routes/api-user-routes.php'));


        Route::middleware(['web', 'auth', IsAdmin::class, RecordUserActivity::class])
            ->name('admin.')
            ->prefix('st-admin')
            ->group(base_path('routes/web-admin-routes.php'));


        Route::middleware(['web', 'auth', RecordUserActivity::class])
            ->name('user.')
            ->group(base_path('routes/web-user-routes.php'));
    }
}
