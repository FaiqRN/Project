<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!session()->has('level_nama') || session('level_nama') !== $role) {
            return redirect('login')->with('error', 'Unauthorized access');
        }
        return $next($request);
    }
}