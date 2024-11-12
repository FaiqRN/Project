<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_id')) {
            return redirect('login')->with('error', 'Silahkan login terlebih dahulu');
        }
        return $next($request);

        $response->header('Cache-Control','nocache, no-store, max-age=0, must-revalidate');
        $response->header('Pragma','no-cache');
        $response->header('Expires','Fri, 01 Jan 1990 00:00:00 GMT');
        
        return $response;
    }
}