<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Bajas', description: 'Bajas de activos. Al dar de baja se cierra la asignación vigente.')]
class BajaDocs
{
    #[OA\Get(
        path: '/api/baja',
        operationId: 'baja_listar',
        summary: 'Listar bajas',
        description: 'Permiso requerido: `baja_listar`.',
        tags: ['Bajas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\QueryParameter(name: 'incluir_bajas', required: false, description: 'Incluir los registros que están en la papelera', schema: new OA\Schema(type: 'boolean')),
            new OA\QueryParameter(name: 'por_pagina', required: false, description: 'Activa la paginación (máx. 100). Sin este parámetro se devuelve la lista completa.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100)),
            new OA\QueryParameter(name: 'page', required: false, description: 'Número de página (solo con por_pagina)', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de registros (arreglo). Con por_pagina: objeto paginado con data, current_page, last_page, total…', content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'object'))),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
        ]
    )]
    public function listar(): void {}

    #[OA\Post(
        path: '/api/baja',
        operationId: 'baja_guardar',
        summary: 'Registrar baja',
        description: 'Marca el activo como "dado_de_baja" y registra la devolución de su asignación vigente con la fecha de baja.

Permiso requerido: `baja_guardar`.',
        tags: ['Bajas'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['activo_id', 'fecha_baja', 'motivo_baja'],
                properties: [
                    new OA\Property(
                        property: 'activo_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'fecha_baja',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-10'
                    ),
                    new OA\Property(
                        property: 'motivo_baja',
                        type: 'string',
                        maxLength: 255,
                        example: 'Daño irreparable'
                    ),
                    new OA\Property(
                        property: 'observacion',
                        type: 'string',
                        nullable: true
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Registro creado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
            new OA\Response(response: 422, ref: '#/components/responses/ErrorValidacion'),
        ]
    )]
    public function guardar(): void {}

    #[OA\Get(
        path: '/api/baja/{id}',
        operationId: 'baja_mostrar',
        summary: 'Ver detalle de baja',
        description: 'Permiso requerido: `baja_mostrar`.',
        tags: ['Bajas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Operación exitosa', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
            new OA\Response(response: 404, ref: '#/components/responses/NoEncontrado'),
        ]
    )]
    public function mostrar(): void {}

    #[OA\Put(
        path: '/api/baja/{id}',
        operationId: 'baja_actualizar',
        summary: 'Actualizar baja',
        description: 'Permiso requerido: `baja_actualizar`.',
        tags: ['Bajas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'fecha_baja',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-10'
                    ),
                    new OA\Property(
                        property: 'motivo_baja',
                        type: 'string',
                        maxLength: 255,
                        example: 'Daño irreparable'
                    ),
                    new OA\Property(
                        property: 'observacion',
                        type: 'string',
                        nullable: true
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Operación exitosa', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
            new OA\Response(response: 404, ref: '#/components/responses/NoEncontrado'),
            new OA\Response(response: 422, ref: '#/components/responses/ErrorValidacion'),
        ]
    )]
    public function actualizar(): void {}

    #[OA\Delete(
        path: '/api/baja/{id}',
        operationId: 'baja_eliminar',
        summary: 'Enviar baja a la papelera',
        description: 'Anula la baja: el activo vuelve al estado que le corresponde según su depreciación.

Permiso requerido: `baja_eliminar`.',
        tags: ['Bajas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Operación exitosa', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
            new OA\Response(response: 404, ref: '#/components/responses/NoEncontrado'),
        ]
    )]
    public function eliminar(): void {}

    #[OA\Post(
        path: '/api/baja/{id}/restaurar',
        operationId: 'baja_restaurar',
        summary: 'Restaurar baja de la papelera',
        description: 'Responde 422 si el activo tiene otra baja vigente o una asignación activa.

Permiso requerido: `baja_restaurar`.',
        tags: ['Bajas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Operación exitosa', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
            new OA\Response(response: 404, ref: '#/components/responses/NoEncontrado'),
            new OA\Response(response: 422, ref: '#/components/responses/ErrorValidacion'),
        ]
    )]
    public function restaurar(): void {}
}
