<?php

namespace App\Http\Middleware;

use Closure;

class Demo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( env('PREVIEW') && !auth()->guard('api')->user()->is_admin ) {
            return response()->json(['type' => 'demo'], 405);
        }
        return $next($request);
    }
}

