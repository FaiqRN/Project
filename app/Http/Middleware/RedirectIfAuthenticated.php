<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('user_id')) {
            $levelName = strtolower(session('level_nama'));
            switch ($levelName) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'kaprodi':
                    return redirect('/kaprodi/dashboard');
                default:
                    return redirect('/dosen/dashboard');
            }
        }
        return $next($request);
    }
}