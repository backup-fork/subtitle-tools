<?php

namespace App\Providers;

use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    public function map()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web-guest-routes.php'));


        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/redirects.php'));


        Route::middleware('api')
            ->name('api.')
            ->prefix('api/v1/')
            ->group(base_path('routes/api-guest-routes.php'));


        Route::middleware(['api', 'auth:api'])
            ->name('api.user.')
            ->prefix('api/v1/')
            ->group(base_path('routes/api-user-routes.php'));


        Route::middleware(['web', 'auth', IsAdmin::class])
            ->namespace($this->namespace.'\Admin')
            ->name('admin.')
            ->prefix('st-admin')
            ->group(base_path('routes/web-admin-routes.php'));


        Route::middleware(['web', 'auth'])
            ->name('user.')
            ->group(base_path('routes/web-user-routes.php'));
    }
}
