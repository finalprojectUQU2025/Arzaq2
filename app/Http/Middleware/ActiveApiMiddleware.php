<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = session()->get('guard', 'accountApi');
        if (auth($guard)->user()->status === 'blocked') {
            return response()->json(['statue' =>  false, 'message' => 'الحساب محظور الرجاء التواصل مع الدعم'], Response::HTTP_BAD_REQUEST);
        }
        return $next($request);
    }
}
