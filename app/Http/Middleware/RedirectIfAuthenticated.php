<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        return $user->is_admin
            ? redirect()->route('admin.dashboard.index')
            : redirect()->route('user.dashboard.index');
    }
}
