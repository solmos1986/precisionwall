<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        return next($request)
        // cabecera CORS para permitir el origen de solicitud desde localhost
        ->header('Access-Control-Allow-Origin', '*');
    }
}
