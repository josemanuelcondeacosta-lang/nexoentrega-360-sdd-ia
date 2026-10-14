<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiContext
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->header('X-Correlation-Id');
        $request->attributes->set('correlationId', Str::isUuid($id ?? '') ? $id : (string) Str::uuid());
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json');
        }
        $response = $next($request);
        $response->headers->set('X-Correlation-Id', $request->attributes->get('correlationId'));
        if ($request->is('api/*')) {
            Log::channel('nexo')->info('api_response', [
                'correlationId' => $request->attributes->get('correlationId'), 'status' => $response->getStatusCode(),
                'method' => $request->method(), 'route' => $request->route()?->uri()]);
        }

        return $response;
    }
}
