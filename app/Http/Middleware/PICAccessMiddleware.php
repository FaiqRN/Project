<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PICAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->level_nama !== 'PIC') {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Hanya PIC yang dapat mengakses fitur ini.'
                ], 403);
            }
            
            return redirect()->route('login')
                ->with('error', 'Unauthorized. Hanya PIC yang dapat mengakses fitur ini.');
        }

        return $next($request);
    }
}