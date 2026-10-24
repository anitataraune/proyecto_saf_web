<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'usuario.activo' => \App\Http\Middleware\VerificarUsuarioActivo::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Respuestas de error de la API en español y con la clave 'mensaje' (se mantiene 'message' por compatibilidad)
        $responder = fn (string $mensaje, int $estado, array $extra = []) => response()->json(
            ['mensaje' => $mensaje, 'message' => $mensaje] + $extra,
            $estado
        );

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'mensaje' => $e->validator->errors()->first(),
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], $e->status);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                return $responder('Sesión no válida o expirada. Inicie sesión nuevamente.', 401);
            }
        });

        $exceptions->render(function (UnauthorizedException $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                return $responder('No tiene permisos para realizar esta acción.', 403);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                return $responder('Demasiados intentos. Espere un momento e intente nuevamente.', 429);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                $mensaje = $e->getPrevious() instanceof ModelNotFoundException
                    ? 'El registro solicitado no existe o fue eliminado.'
                    : 'La ruta solicitada no existe.';

                return $responder($mensaje, 404);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                $porDefecto = [
                    403 => 'No tiene permisos para realizar esta acción.',
                    405 => 'Método no permitido para esta ruta.',
                    413 => 'El archivo enviado es demasiado grande.',
                ];

                // abort(403, 'mensaje') conserva su mensaje propio
                return $responder($e->getMessage() ?: ($porDefecto[$e->getStatusCode()] ?? 'No se pudo procesar la solicitud.'), $e->getStatusCode());
            }
        });

        // En producción no se exponen detalles internos; con APP_DEBUG=true se mantiene la traza para depurar
        $exceptions->render(function (Throwable $e, Request $request) use ($responder) {
            if ($request->is('api/*') && ! config('app.debug')) {
                return $responder('Ocurrió un error interno en el servidor. Intente nuevamente.', 500);
            }
        });
    })->create();
