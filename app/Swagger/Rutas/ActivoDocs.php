<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Activos', description: 'Activos fijos de la institución.')]
class ActivoDocs
{
    #[OA\Get(
        path: '/api/activo/disponibles',
        operationId: 'activo_listarDisponibles',
        summary: 'Listar activos disponibles para asignar',
        description: 'Activos que no están dados de baja y no tienen una asignación vigente.

Permiso requerido: `activo_listar`.',
        tags: ['Activos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\QueryParameter(name: 'por_pagina', required: false, description: 'Activa la paginación (máx. 100). Sin este parámetro se devuelve la lista completa.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100)),
            new OA\QueryParameter(name: 'page', required: false, description: 'Número de página (solo con por_pagina)', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de registros (arreglo). Con por_pagina: objeto paginado con data, current_page, last_page, total…', content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'object'))),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
        ]
    )]
    public function listarDisponibles(): void {}

    #[OA\Get(
        path: '/api/activo',
        operationId: 'activo_listar',
        summary: 'Listar activos',
        description: 'Permiso requerido: `activo_listar`.',
        tags: ['Activos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\QueryParameter(name: 'eliminados', required: false, description: 'Mostrar solo los registros que están en la papelera', schema: new OA\Schema(type: 'boolean')),
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
        path: '/api/activo',
        operationId: 'activo_guardar',
        summary: 'Registrar activo',
        description: 'Un activo nuevo siempre inicia con estado "activo".

Permiso requerido: `activo_guardar`.',
        tags: ['Activos'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['descripcion', 'valor_compra', 'fecha_compra', 'categoria_id'],
                properties: [
                    new OA\Property(
                        property: 'descripcion',
                        type: 'string',
                        maxLength: 255,
                        example: 'Laptop Dell Latitude 5440'
                    ),
                    new OA\Property(
                        property: 'valor_compra',
                        type: 'number',
                        minimum: 0,
                        example: 8500.5
                    ),
                    new OA\Property(
                        property: 'fecha_compra',
                        type: 'string',
                        format: 'date',
                        example: '2026-01-15'
                    ),
                    new OA\Property(
                        property: 'categoria_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'ubicacion_id',
                        type: 'integer',
                        nullable: true,
                        example: 1
                    ),
                    new OA\Property(
                        property: 'proveedor_id',
                        type: 'integer',
                        nullable: true,
                        example: 1
                    ),
                    new OA\Property(
                        property: 'estado_actual',
                        type: 'string',
                        enum: ['activo'],
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
        path: '/api/activo/{id}',
        operationId: 'activo_mostrar',
        summary: 'Ver detalle de activo',
        description: 'Permiso requerido: `activo_mostrar`.',
        tags: ['Activos'],
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
        path: '/api/activo/{id}',
        operationId: 'activo_actualizar',
        summary: 'Actualizar activo',
        description: 'El estado no se edita manualmente (lo cambian Bajas y Depreciación). Con depreciaciones registradas no se puede cambiar el valor, la fecha de compra ni la categoría.

Permiso requerido: `activo_actualizar`.',
        tags: ['Activos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'descripcion',
                        type: 'string',
                        maxLength: 255,
                        example: 'Laptop Dell Latitude 5440'
                    ),
                    new OA\Property(
                        property: 'valor_compra',
                        type: 'number',
                        minimum: 0,
                        example: 8500.5
                    ),
                    new OA\Property(
                        property: 'fecha_compra',
                        type: 'string',
                        format: 'date',
                        example: '2026-01-15'
                    ),
                    new OA\Property(
                        property: 'estado_actual',
                        type: 'string',
                        enum: ['activo', 'depreciado', 'dado_de_baja']
                    ),
                    new OA\Property(
                        property: 'categoria_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'ubicacion_id',
                        type: 'integer',
                        nullable: true,
                        example: 1
                    ),
                    new OA\Property(
                        property: 'proveedor_id',
                        type: 'integer',
                        nullable: true,
                        example: 1
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
        path: '/api/activo/{id}',
        operationId: 'activo_eliminar',
        summary: 'Enviar activo a la papelera',
        description: 'Responde 422 si el activo tiene una asignación vigente.

Permiso requerido: `activo_eliminar`.',
        tags: ['Activos'],
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
    public function eliminar(): void {}

    #[OA\Post(
        path: '/api/activo/{id}/restaurar',
        operationId: 'activo_restaurar',
        summary: 'Restaurar activo de la papelera',
        description: 'Permiso requerido: `activo_restaurar`.',
        tags: ['Activos'],
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
    public function restaurar(): void {}
}
