<?php

namespace App\Http\Middleware;

use Closure;

class RecordUserActivity
{
    public function handle($request, Closure $next)
    {
        $user = user();

        $user->timestamps = false;

        $user->update([
            'last_seen_at' => now(),
        ]);

        $user->timestamps = true;

        return $next($request);
    }
}
