<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated{
    public function handle(Request $request, Closure $next): Response{
        // Hanya redirect jika mencoba mengakses halaman login/register
        if (session()->has('user_id') && ($request->is('login') || $request->is('register'))) {
            $levelName = strtolower(session('level_nama'));
            switch ($levelName) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'kaprodi':
                    return redirect()->route('kaprodi.dashboard');
                default:
                    return redirect()->route('dosen.dashboard');
            }
        }
        return $next($request);
    }
}