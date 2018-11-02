<?php

namespace App\Http\Middleware;

use Closure;

class UserVerify
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
        echo '路由中间件';
        return $next($request);
    }
}
