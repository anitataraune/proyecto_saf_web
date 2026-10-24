<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Configuración', description: 'Identidad visual y datos de la empresa.')]
class ConfiguracionDocs
{
    #[OA\Get(
        path: '/api/configuracion',
        operationId: 'configuracion_obtenerConfiguracion',
        summary: 'Obtener la configuración del sistema',
        tags: ['Configuración'],
        responses: [
            new OA\Response(response: 200, description: 'Operación exitosa', content: new OA\JsonContent(type: 'object')),
        ]
    )]
    public function obtenerConfiguracion(): void {}

    #[OA\Post(
        path: '/api/configuracion',
        operationId: 'configuracion_actualizarConfiguracion',
        summary: 'Actualizar la configuración y el logo',
        description: 'Permiso requerido: `configuracion_actualizar`.',
        tags: ['Configuración'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'nombre_sistema',
                            type: 'string',
                            maxLength: 255,
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'nombre_empresa',
                            type: 'string',
                            maxLength: 255,
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'nit_empresa',
                            type: 'string',
                            maxLength: 50,
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'direccion',
                            type: 'string',
                            maxLength: 255,
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'telefono',
                            type: 'string',
                            maxLength: 50,
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'logo',
                            type: 'string',
                            format: 'binary',
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'color_primario',
                            type: 'string',
                            pattern: '^#[0-9A-Fa-f]{6}$',
                            nullable: true,
                            example: '#2563EB'
                        ),
                        new OA\Property(
                            property: 'color_secundario',
                            type: 'string',
                            pattern: '^#[0-9A-Fa-f]{6}$',
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'color_fondo',
                            type: 'string',
                            pattern: '^#[0-9A-Fa-f]{6}$',
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'color_superficie',
                            type: 'string',
                            pattern: '^#[0-9A-Fa-f]{6}$',
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'color_texto',
                            type: 'string',
                            pattern: '^#[0-9A-Fa-f]{6}$',
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'fuente',
                            type: 'string',
                            maxLength: 100,
                            nullable: true
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Operación exitosa', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
            new OA\Response(response: 422, ref: '#/components/responses/ErrorValidacion'),
        ]
    )]
    public function actualizarConfiguracion(): void {}
}
