<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Beneficiary
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->user_type == 'beneficiary') {
            return $next($request);
        } else if (auth()->user()->user_type == 'staff') {
            return redirect()->route('admin.home');
        } else {
            return redirect()->route('home');
        }  
    }
}
