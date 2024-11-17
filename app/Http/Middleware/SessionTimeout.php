<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    protected $session;
    protected $timeout = 1200; 

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $isLoggedIn = $request->path() != 'logout';
        
        if (!session('last_activity') && $isLoggedIn) {
            $this->session->put('last_activity', time());
        }
        
        if (session('last_activity') && time() - session('last_activity') > $this->timeout) {
            $this->session->forget('last_activity');
            $this->session->forget(['user_id', 'username', 'level_id', 'level_nama', 'nama_lengkap']);
            auth()->logout();
            return redirect()->route('login')->with('timeout', 'Sesi Anda telah berakhir');
        }

        // Jangan simpan request yang berisi file ke session
        if (!$request->hasFile('foto')) {
            $this->session->put('last_activity', time());
        }

        return $next($request);
    }
}