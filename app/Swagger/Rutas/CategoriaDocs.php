<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Categorías', description: 'Categorías de activos con su vida útil (en años).')]
class CategoriaDocs
{
    #[OA\Get(
        path: '/api/categoria',
        operationId: 'categoria_listar',
        summary: 'Listar categorías',
        description: 'Permiso requerido: `categoria_listar`.',
        tags: ['Categorías'],
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
        path: '/api/categoria',
        operationId: 'categoria_guardar',
        summary: 'Registrar categoría',
        description: 'Permiso requerido: `categoria_guardar`.',
        tags: ['Categorías'],
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
                        property: 'descripcion',
                        type: 'string',
                        nullable: true,
                        example: 'Laptop Dell Latitude 5440'
                    ),
                    new OA\Property(
                        property: 'vida_util',
                        type: 'integer',
                        minimum: 1,
                        maximum: 100,
                        nullable: true,
                        example: 4
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
        path: '/api/categoria/{id}',
        operationId: 'categoria_mostrar',
        summary: 'Ver detalle de categoría',
        description: 'Permiso requerido: `categoria_mostrar`.',
        tags: ['Categorías'],
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
        path: '/api/categoria/{id}',
        operationId: 'categoria_actualizar',
        summary: 'Actualizar categoría',
        description: 'Permiso requerido: `categoria_actualizar`.',
        tags: ['Categorías'],
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
                        property: 'descripcion',
                        type: 'string',
                        nullable: true,
                        example: 'Laptop Dell Latitude 5440'
                    ),
                    new OA\Property(
                        property: 'vida_util',
                        type: 'integer',
                        minimum: 1,
                        maximum: 100,
                        nullable: true,
                        example: 4
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
        path: '/api/categoria/{id}',
        operationId: 'categoria_eliminar',
        summary: 'Enviar categoría a la papelera',
        description: 'Responde 422 si la categoría tiene activos registrados.

Permiso requerido: `categoria_eliminar`.',
        tags: ['Categorías'],
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
        path: '/api/categoria/{id}/restaurar',
        operationId: 'categoria_restaurar',
        summary: 'Restaurar categoría de la papelera',
        description: 'Permiso requerido: `categoria_restaurar`.',
        tags: ['Categorías'],
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
