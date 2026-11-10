<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // La API no tiene página de login.
        // Si falta el token, responde con error 401.
        $middleware->redirectGuestsTo(fn () => null);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Identificar las solicitudes de la API.
        $exceptions->shouldRenderJsonWhen(
            function (Request $request, \Throwable $e) {
                return $request->is('api/*') || $request->expectsJson();
            }
        );

        // 401: Usuario no autenticado.
        $exceptions->render(function (
            AuthenticationException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response(
                    'No estás autenticado.',
                    401
                )->header('Content-Type', 'text/plain; charset=UTF-8');
            }

            return null;
        });

        // 403: Acceso denegado.
        $exceptions->render(function (
            AccessDeniedHttpException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response(
                    'No tienes autorización para realizar esta acción.',
                    403
                )->header('Content-Type', 'text/plain; charset=UTF-8');
            }

            return null;
        });

        // 404: Recurso no encontrado.
        $exceptions->render(function (
            NotFoundHttpException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response(
                    'El recurso solicitado no existe.',
                    404
                )->header('Content-Type', 'text/plain; charset=UTF-8');
            }

            return null;
        });

        // 405: Método HTTP incorrecto.
        $exceptions->render(function (
            MethodNotAllowedHttpException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response(
                    'El método HTTP utilizado no está permitido.',
                    405
                )->header('Content-Type', 'text/plain; charset=UTF-8');
            }

            return null;
        });

        // 422: Error de validación.
        $exceptions->render(function (
            ValidationException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {

                // Mensaje especial para errores de autenticación.
                if (
                    $request->is('api/v1/auth/login') &&
                    $e->errors() === [
                        'email' => ['Las credenciales no son correctas.'],
                    ]
                ) {
                    return response(
                        'Las credenciales no son correctas.',
                        422
                    )->header('Content-Type', 'text/plain; charset=UTF-8');
                }

                // Mensaje general para los demás errores de validación.
                return response(
                    'Los datos enviados no son válidos.',
                    422
                )->header('Content-Type', 'text/plain; charset=UTF-8');
            }

            return null;
        });

        // 429: Demasiados intentos.
        $exceptions->render(function (
            ThrottleRequestsException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response(
                    'Has realizado demasiados intentos. Inténtalo más tarde.',
                    429
                )->header('Content-Type', 'text/plain; charset=UTF-8');
            }

            return null;
        });

    })
    ->create();
