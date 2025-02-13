<?php

namespace App\Http\Middleware;

use Closure;

class Ticket
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
        return $next($request);
        try {
            $role = json_decode(auth()->user()->Rol_ID);

            foreach ($role as $val) {
                if ($val == 2) {
                    return $next($request);
                    break;
                }
            }
        } catch (\Throwable$th) {
            return route('home');
        }

    }
}
