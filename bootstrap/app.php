<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, \Throwable $exception, Request $request) {
        $status = $response->getStatusCode();

        if ($request->expectsJson()) {
            return $response;
        }

        if ($status === 419) {
            return back()->with('error', 'The page expired. Please try again.');
        }

        if (in_array($status, [403, 404, 500, 503], true)) {
            if (app()->environment(['local', 'testing']) && in_array($status, [500, 503], true)) {
                return $response;
            }

            return Inertia::render('Errors/Error', [
                'status' => $status,
            ])
                ->toResponse($request)
                ->setStatusCode($status);
        }

        return $response;
    });
    })->create();