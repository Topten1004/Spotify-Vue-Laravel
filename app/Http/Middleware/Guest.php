<?php

namespace App\Http\Middleware;

use Closure;

class Guest
{
    /**
     * Check if the user is logged or not, if not then he is considered as a guest.
     *
     * @var array
     */
    public function handle($request, Closure $next)
    {
        $requireAuth = \App\Setting::get('requireAuth');
        if ($requireAuth && !auth()->guard('api')->user()) {
            return abort(404);
        }
        return $next($request);
    }
}
