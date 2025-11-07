<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\ThrottleRequests::with('60,1'),
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Автоматически возвращать JSON для API
        $exceptions->shouldRenderJsonWhen(function ($request) {
            return $request->is('api/*');
        });
        
        // Кастомная обработка всех ошибок для API
        $exceptions->render(function (\Throwable $exception, $request) {
            if ($request->is('api/*')) {
                $statusCode = method_exists($exception, 'getStatusCode')
                    ? $exception?->getStatusCode()
                    : 500;
                
                $message = $exception->getMessage();
               
                return response()->json($message, $statusCode);
            }
        });
    })->create();