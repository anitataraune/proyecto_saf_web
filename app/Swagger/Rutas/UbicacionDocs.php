<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Ubicaciones', description: 'Ubicaciones físicas de los activos.')]
class UbicacionDocs
{
    #[OA\Get(
        path: '/api/ubicacion',
        operationId: 'ubicacion_listar',
        summary: 'Listar ubicaciones',
        description: 'Permiso requerido: `ubicacion_listar`.',
        tags: ['Ubicaciones'],
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
        path: '/api/ubicacion',
        operationId: 'ubicacion_guardar',
        summary: 'Registrar ubicación',
        description: 'Permiso requerido: `ubicacion_guardar`.',
        tags: ['Ubicaciones'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre_ubicacion'],
                properties: [
                    new OA\Property(
                        property: 'nombre_ubicacion',
                        type: 'string',
                        maxLength: 255,
                        example: 'Oficina de Contabilidad'
                    ),
                    new OA\Property(
                        property: 'descripcion_ubicacion',
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
        path: '/api/ubicacion/{id}',
        operationId: 'ubicacion_mostrar',
        summary: 'Ver detalle de ubicación',
        description: 'Permiso requerido: `ubicacion_mostrar`.',
        tags: ['Ubicaciones'],
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
        path: '/api/ubicacion/{id}',
        operationId: 'ubicacion_actualizar',
        summary: 'Actualizar ubicación',
        description: 'Permiso requerido: `ubicacion_actualizar`.',
        tags: ['Ubicaciones'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'nombre_ubicacion',
                        type: 'string',
                        maxLength: 255,
                        example: 'Oficina de Contabilidad'
                    ),
                    new OA\Property(
                        property: 'descripcion_ubicacion',
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
        path: '/api/ubicacion/{id}',
        operationId: 'ubicacion_eliminar',
        summary: 'Enviar ubicación a la papelera',
        description: 'Responde 422 si la ubicación tiene activos registrados.

Permiso requerido: `ubicacion_eliminar`.',
        tags: ['Ubicaciones'],
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
        path: '/api/ubicacion/{id}/restaurar',
        operationId: 'ubicacion_restaurar',
        summary: 'Restaurar ubicación de la papelera',
        description: 'Permiso requerido: `ubicacion_restaurar`.',
        tags: ['Ubicaciones'],
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
