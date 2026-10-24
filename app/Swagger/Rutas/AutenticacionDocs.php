<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Autenticación', description: 'Inicio y cierre de sesión con token Bearer (Sanctum).')]
class AutenticacionDocs
{
    #[OA\Post(
        path: '/api/autenticacion/iniciar-sesion',
        operationId: 'autenticacion_iniciarSesion',
        summary: 'Iniciar sesión y obtener token',
        tags: ['Autenticación'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['usuario', 'clave'],
                properties: [
                    new OA\Property(
                        property: 'usuario',
                        type: 'string',
                        example: 'admin'
                    ),
                    new OA\Property(
                        property: 'clave',
                        type: 'string',
                        example: 'secreta123'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Token de acceso, roles y permisos del usuario', content: new OA\JsonContent(example: ['mensaje' => 'Inicio de sesión exitoso', 'access_token' => '1|abc123...', 'token_type' => 'Bearer', 'usuario' => ['id_usuario' => 1, 'usuario' => 'admin', 'es_admin' => true, 'roles' => ['administrador'], 'permisos' => ['activo_listar']]])),
            new OA\Response(response: 401, description: 'Credenciales incorrectas', content: new OA\JsonContent(ref: '#/components/schemas/Mensaje')),
            new OA\Response(response: 403, description: 'Usuario desactivado', content: new OA\JsonContent(ref: '#/components/schemas/Mensaje')),
            new OA\Response(response: 429, description: 'Demasiados intentos (máx. 5 por minuto)', content: new OA\JsonContent(ref: '#/components/schemas/Mensaje')),
        ]
    )]
    public function iniciarSesion(): void {}

    #[OA\Post(
        path: '/api/autenticacion/cerrar-sesion',
        operationId: 'autenticacion_cerrarSesion',
        summary: 'Cerrar sesión (revoca el token actual)',
        tags: ['Autenticación'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Operación exitosa', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
        ]
    )]
    public function cerrarSesion(): void {}
}
