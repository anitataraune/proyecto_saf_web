<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Proveedores', description: 'Proveedores de los activos.')]
class ProveedorDocs
{
    #[OA\Get(
        path: '/api/proveedor',
        operationId: 'proveedor_listar',
        summary: 'Listar proveedores',
        description: 'Permiso requerido: `proveedor_listar`.',
        tags: ['Proveedores'],
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
        path: '/api/proveedor',
        operationId: 'proveedor_guardar',
        summary: 'Registrar proveedor',
        description: 'Permiso requerido: `proveedor_guardar`.',
        tags: ['Proveedores'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre'],
                properties: [
                    new OA\Property(
                        property: 'nombre',
                        type: 'string',
                        maxLength: 255,
                        example: 'EQUIPOS DE COMPUTACION'
                    ),
                    new OA\Property(
                        property: 'apellido',
                        type: 'string',
                        maxLength: 255,
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'nit',
                        type: 'string',
                        maxLength: 50,
                        nullable: true,
                        example: '1234567019'
                    ),
                    new OA\Property(
                        property: 'direccion',
                        type: 'string',
                        maxLength: 255,
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
        path: '/api/proveedor/{id}',
        operationId: 'proveedor_mostrar',
        summary: 'Ver detalle de proveedor',
        description: 'Permiso requerido: `proveedor_mostrar`.',
        tags: ['Proveedores'],
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
        path: '/api/proveedor/{id}',
        operationId: 'proveedor_actualizar',
        summary: 'Actualizar proveedor',
        description: 'Permiso requerido: `proveedor_actualizar`.',
        tags: ['Proveedores'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'nombre',
                        type: 'string',
                        maxLength: 255,
                        example: 'EQUIPOS DE COMPUTACION'
                    ),
                    new OA\Property(
                        property: 'apellido',
                        type: 'string',
                        maxLength: 255,
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'nit',
                        type: 'string',
                        maxLength: 50,
                        nullable: true,
                        example: '1234567019'
                    ),
                    new OA\Property(
                        property: 'direccion',
                        type: 'string',
                        maxLength: 255,
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
        path: '/api/proveedor/{id}',
        operationId: 'proveedor_eliminar',
        summary: 'Enviar proveedor a la papelera',
        description: 'Responde 422 si el proveedor tiene activos registrados.

Permiso requerido: `proveedor_eliminar`.',
        tags: ['Proveedores'],
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
        path: '/api/proveedor/{id}/restaurar',
        operationId: 'proveedor_restaurar',
        summary: 'Restaurar proveedor de la papelera',
        description: 'Permiso requerido: `proveedor_restaurar`.',
        tags: ['Proveedores'],
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
