<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (! $request->user()) {
            return redirect()
                ->route('login');
        }

        if (
            $request->user()->role !== 'admin' ||
            $request->user()->status !== 'active'
        ) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Anda tidak memiliki akses ke halaman administrator.'
                );
        }

        return $next($request);
    }
}