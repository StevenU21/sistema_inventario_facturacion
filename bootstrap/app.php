<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->json()) {
                return response()->json(['message' => 'Resouce was not found'], 404);
            }
            throw $e;
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(['message' => 'You are not authenticated, please register or log in.'], 401);
        });

        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return response()->json(['message' => 'You do not have the required authorization to perform this action.'], 403);
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            return response()->json(['message' => 'Your email address is not verified.'], 403);

        });
    })->create();
