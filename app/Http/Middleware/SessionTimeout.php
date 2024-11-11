<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    protected $session;
    protected $timeout = 60; // 30 minutes in seconds

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!session('last_activity')) {
            $this->session->put('last_activity', time());
        }
        elseif (time() - $this->session->get('last_activity') > $this->timeout) {
            $this->session->flush();
            return redirect('login')->with('error', 'Sesi Anda telah berakhir. Silahkan login kembali.');
        }

        $this->session->put('last_activity', time());

        return $next($request);
    }
}