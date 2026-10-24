<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Asignaciones', description: 'Asignación y devolución de activos a responsables.')]
class AsignacionDocs
{
    #[OA\Get(
        path: '/api/asignacion',
        operationId: 'asignacion_listar',
        summary: 'Listar asignaciones',
        description: 'Permiso requerido: `asignacion_listar`.',
        tags: ['Asignaciones'],
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
        path: '/api/asignacion',
        operationId: 'asignacion_guardar',
        summary: 'Registrar asignación',
        description: 'El activo no puede estar asignado ni dado de baja, y la fecha no puede ser anterior a su compra.

Permiso requerido: `asignacion_guardar`.',
        tags: ['Asignaciones'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['activo_id', 'responsable_id', 'fecha_asignacion'],
                properties: [
                    new OA\Property(
                        property: 'activo_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'responsable_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'fecha_asignacion',
                        type: 'string',
                        format: 'date',
                        example: '2026-02-01'
                    ),
                    new OA\Property(
                        property: 'observacion',
                        type: 'string',
                        maxLength: 500,
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
        path: '/api/asignacion/{id}',
        operationId: 'asignacion_mostrar',
        summary: 'Ver detalle de asignación',
        description: 'Permiso requerido: `asignacion_listar`.',
        tags: ['Asignaciones'],
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

    #[OA\Post(
        path: '/api/asignacion/{id}/devolucion',
        operationId: 'asignacion_registrarDevolucion',
        summary: 'Registrar la devolución del activo',
        description: 'Permiso requerido: `asignacion_devolver`.',
        tags: ['Asignaciones'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_devolucion'],
                properties: [
                    new OA\Property(
                        property: 'fecha_devolucion',
                        type: 'string',
                        format: 'date',
                        example: '2026-06-30'
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
    public function registrarDevolucion(): void {}

    #[OA\Put(
        path: '/api/asignacion/{id}',
        operationId: 'asignacion_actualizar',
        summary: 'Actualizar asignación',
        description: 'Permiso requerido: `asignacion_editar`.',
        tags: ['Asignaciones'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'responsable_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'fecha_asignacion',
                        type: 'string',
                        format: 'date',
                        example: '2026-02-01'
                    ),
                    new OA\Property(
                        property: 'observacion',
                        type: 'string',
                        maxLength: 500,
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
        path: '/api/asignacion/{id}',
        operationId: 'asignacion_eliminar',
        summary: 'Enviar asignación a la papelera',
        description: 'Permiso requerido: `asignacion_eliminar`.',
        tags: ['Asignaciones'],
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
        path: '/api/asignacion/{id}/restaurar',
        operationId: 'asignacion_restaurar',
        summary: 'Restaurar asignación de la papelera',
        description: 'Una asignación vigente solo se restaura si el activo sigue disponible.

Permiso requerido: `asignacion_restaurar`.',
        tags: ['Asignaciones'],
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
