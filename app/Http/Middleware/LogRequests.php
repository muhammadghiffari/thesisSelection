<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Masuk ke route:', [
            'url'    => $request->fullUrl(),
            'method' => $request->method(),
            'params' => $request->all()
        ]);

        return $next($request);
    }
}
