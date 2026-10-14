<?php

use App\Http\Middleware\ApiContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(ApiContext::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            if (request()->is('api/*')) {
                Log::channel('nexo')->error('api_failure', ['correlationId' => request()->attributes->get('correlationId'), 'type' => get_class($e)]);

                return false;
            }
        });
        $exceptions->render(function (Throwable $e, Request $r) {
            if (! $r->is('api/*')) {
                return null;
            }
            $status = $e instanceof ValidationException ? 422 : ($e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500);
            $body = ['code' => 'HTTP_'.$status, 'message' => $status === 500 ? 'Error interno; la operación no se confirmó.' : ($e instanceof ValidationException ? 'Revisa los campos indicados.' : $e->getMessage()), 'correlationId' => $r->attributes->get('correlationId')];
            if ($e instanceof ValidationException) {
                $body['field'] = array_key_first($e->errors());
            }
            Log::channel('nexo')->info('api_rejected', ['correlationId' => $body['correlationId'], 'status' => $status]);

            return response()->json($body, $status)->header('X-Correlation-Id', $body['correlationId']);
        });
    })->create();
