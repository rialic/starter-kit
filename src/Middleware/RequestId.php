<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestId
{
    public function handle(Request $request, Closure $next): mixed
    {
        $requestId = $request->header('X-Request-ID') ?? uniqid();
        $request->headers->set('X-Request-ID', $requestId);

        $response = $next($request);
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
