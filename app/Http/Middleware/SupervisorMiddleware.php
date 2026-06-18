<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SupervisorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isSupervisor()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Supervisor.');
        }
        return $next($request);
    }
}