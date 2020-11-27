<?php

namespace App\Http\Middleware;

use Closure;
use GrahamCampbell\Throttle\Facades\Throttle;
use Illuminate\Http\Request;

class Hammering
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Throttle::check($request)) {
            abort(429, 'TOO MANY ATTEMPTS');
        }

        return $next($request);
    }
}
