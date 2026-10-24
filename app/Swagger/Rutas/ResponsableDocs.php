<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Responsables', description: 'Personas a las que se asignan los activos.')]
class ResponsableDocs
{
    #[OA\Get(
        path: '/api/responsable',
        operationId: 'responsable_listar',
        summary: 'Listar responsables',
        description: 'Permiso requerido: `responsable_listar`.',
        tags: ['Responsables'],
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
        path: '/api/responsable',
        operationId: 'responsable_guardar',
        summary: 'Registrar responsable',
        description: 'Permiso requerido: `responsable_guardar`.',
        tags: ['Responsables'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre_responsable', 'apellido_responsable', 'cargo_responsable'],
                properties: [
                    new OA\Property(
                        property: 'nombre_responsable',
                        type: 'string',
                        maxLength: 255,
                        example: 'Ana'
                    ),
                    new OA\Property(
                        property: 'apellido_responsable',
                        type: 'string',
                        maxLength: 255,
                        example: 'Pérez'
                    ),
                    new OA\Property(
                        property: 'cargo_responsable',
                        type: 'string',
                        maxLength: 255,
                        example: 'Contadora'
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
        path: '/api/responsable/{id}',
        operationId: 'responsable_mostrar',
        summary: 'Ver detalle de responsable',
        description: 'Permiso requerido: `responsable_mostrar`.',
        tags: ['Responsables'],
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
        path: '/api/responsable/{id}',
        operationId: 'responsable_actualizar',
        summary: 'Actualizar responsable',
        description: 'Permiso requerido: `responsable_actualizar`.',
        tags: ['Responsables'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'nombre_responsable',
                        type: 'string',
                        maxLength: 255,
                        example: 'Ana'
                    ),
                    new OA\Property(
                        property: 'apellido_responsable',
                        type: 'string',
                        maxLength: 255,
                        example: 'Pérez'
                    ),
                    new OA\Property(
                        property: 'cargo_responsable',
                        type: 'string',
                        maxLength: 255,
                        example: 'Contadora'
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
        path: '/api/responsable/{id}',
        operationId: 'responsable_eliminar',
        summary: 'Enviar responsable a la papelera',
        description: 'Responde 422 si el responsable tiene activos asignados.

Permiso requerido: `responsable_eliminar`.',
        tags: ['Responsables'],
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
        path: '/api/responsable/{id}/restaurar',
        operationId: 'responsable_restaurar',
        summary: 'Restaurar responsable de la papelera',
        description: 'Permiso requerido: `responsable_restaurar`.',
        tags: ['Responsables'],
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
