<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Ambil level user dari session dan ubah ke lowercase untuk konsistensi
        $userLevel = strtolower(session('level_nama'));
        
        // Konversi semua roles yang diizinkan ke lowercase
        $roles = array_map('strtolower', $roles);
        
        // Cek apakah level user ada dalam daftar roles yang diizinkan
        if (!in_array($userLevel, $roles)) {
            if ($request->ajax()) {
                return response('Unauthorized.', 403);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
        }

        return $next($request);
    }
}