<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = session()->get('guard', 'admin');
        if (auth($guard)->check() && auth($guard)->user()->blocked == true) {
            return redirect()->route('logout');
        }
        return $next($request);
    }
}
