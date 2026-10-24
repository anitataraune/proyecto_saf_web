<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tipo de cambio y UFV', description: 'Cotizaciones diarias; el UFV de cierre de mes se usa en la depreciación.')]
class TipoCambioDocs
{
    #[OA\Get(
        path: '/api/tipo-cambio',
        operationId: 'tipoCambio_listar',
        summary: 'Listar tipo de cambio y ufv',
        description: 'Permiso requerido: `tipo_cambio_listar`.',
        tags: ['Tipo de cambio y UFV'],
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
        path: '/api/tipo-cambio',
        operationId: 'tipoCambio_guardar',
        summary: 'Registrar tipo de cambio',
        description: 'Permiso requerido: `tipo_cambio_guardar`.',
        tags: ['Tipo de cambio y UFV'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha', 'ufvs', 'valor_tc', 'monto'],
                properties: [
                    new OA\Property(
                        property: 'fecha',
                        type: 'string',
                        format: 'date',
                        example: '2026-01-31'
                    ),
                    new OA\Property(
                        property: 'ufvs',
                        type: 'number',
                        example: 3.12345
                    ),
                    new OA\Property(
                        property: 'valor_tc',
                        type: 'number',
                        example: 6.96
                    ),
                    new OA\Property(
                        property: 'monto',
                        type: 'number',
                        minimum: 0,
                        example: 0
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
        path: '/api/tipo-cambio/{id}',
        operationId: 'tipoCambio_mostrar',
        summary: 'Ver detalle de tipo de cambio',
        description: 'Permiso requerido: `tipo_cambio_mostrar`.',
        tags: ['Tipo de cambio y UFV'],
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
        path: '/api/tipo-cambio/{id}',
        operationId: 'tipoCambio_actualizar',
        summary: 'Actualizar tipo de cambio',
        description: 'Permiso requerido: `tipo_cambio_actualizar`.',
        tags: ['Tipo de cambio y UFV'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'fecha',
                        type: 'string',
                        format: 'date',
                        example: '2026-01-31'
                    ),
                    new OA\Property(
                        property: 'ufvs',
                        type: 'number',
                        example: 3.12345
                    ),
                    new OA\Property(
                        property: 'valor_tc',
                        type: 'number',
                        example: 6.96
                    ),
                    new OA\Property(
                        property: 'monto',
                        type: 'number',
                        minimum: 0,
                        example: 0
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
        path: '/api/tipo-cambio/{id}',
        operationId: 'tipoCambio_eliminar',
        summary: 'Enviar tipo de cambio a la papelera',
        description: 'Permiso requerido: `tipo_cambio_eliminar`.',
        tags: ['Tipo de cambio y UFV'],
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
        path: '/api/tipo-cambio/{id}/restaurar',
        operationId: 'tipoCambio_restaurar',
        summary: 'Restaurar tipo de cambio de la papelera',
        description: 'Permiso requerido: `tipo_cambio_restaurar`.',
        tags: ['Tipo de cambio y UFV'],
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
