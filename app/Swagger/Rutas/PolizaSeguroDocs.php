<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Pólizas de seguro', description: 'Pólizas y los activos que cubren.')]
class PolizaSeguroDocs
{
    #[OA\Get(
        path: '/api/poliza-seguro',
        operationId: 'polizaSeguro_listar',
        summary: 'Listar pólizas de seguro',
        description: 'Permiso requerido: `poliza_seguro_listar`.',
        tags: ['Pólizas de seguro'],
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

    #[OA\Get(
        path: '/api/poliza-seguro/activos-agrupados',
        operationId: 'polizaSeguro_catalogosActivosAgrupados',
        summary: 'Activos asegurables agrupados por categoría',
        description: 'Permiso requerido: `poliza_seguro_listar`.',
        tags: ['Pólizas de seguro'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Operación exitosa', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
        ]
    )]
    public function catalogosActivosAgrupados(): void {}

    #[OA\Post(
        path: '/api/poliza-seguro',
        operationId: 'polizaSeguro_guardar',
        summary: 'Registrar póliza',
        description: 'Permiso requerido: `poliza_seguro_guardar`.',
        tags: ['Pólizas de seguro'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['descripcion', 'fecha_inicio', 'fecha_fin'],
                properties: [
                    new OA\Property(
                        property: 'descripcion',
                        type: 'string',
                        example: 'Laptop Dell Latitude 5440'
                    ),
                    new OA\Property(
                        property: 'fecha_inicio',
                        type: 'string',
                        format: 'date',
                        example: '2026-01-01'
                    ),
                    new OA\Property(
                        property: 'fecha_fin',
                        type: 'string',
                        format: 'date',
                        example: '2026-12-31'
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
        path: '/api/poliza-seguro/{id}',
        operationId: 'polizaSeguro_mostrar',
        summary: 'Ver detalle de póliza',
        description: 'Permiso requerido: `poliza_seguro_mostrar`.',
        tags: ['Pólizas de seguro'],
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
        path: '/api/poliza-seguro/{id}',
        operationId: 'polizaSeguro_actualizar',
        summary: 'Actualizar póliza',
        description: 'Permiso requerido: `poliza_seguro_actualizar`.',
        tags: ['Pólizas de seguro'],
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
                        example: 'Laptop Dell Latitude 5440'
                    ),
                    new OA\Property(
                        property: 'fecha_inicio',
                        type: 'string',
                        format: 'date',
                        example: '2026-01-01'
                    ),
                    new OA\Property(
                        property: 'fecha_fin',
                        type: 'string',
                        format: 'date',
                        example: '2026-12-31'
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

    #[OA\Post(
        path: '/api/poliza-seguro/{id}/activos',
        operationId: 'polizaSeguro_sincronizarActivos',
        summary: 'Asignar o quitar activos de la póliza',
        description: 'Envía la lista completa de activos de la póliza: se agregan los nuevos (con la fecha indicada) y se quitan los que no vienen. Los que ya estaban conservan su fecha original.

Permiso requerido: `poliza_seguro_actualizar`.',
        tags: ['Pólizas de seguro'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, description: 'Identificador del registro', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'activos',
                        type: 'array',
                        nullable: true,
                        items: new OA\Items(type: 'integer')
                    ),
                    new OA\Property(
                        property: 'fecha_asignacion',
                        type: 'string',
                        format: 'date',
                        nullable: true,
                        example: '2026-02-01'
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
    public function sincronizarActivos(): void {}

    #[OA\Delete(
        path: '/api/poliza-seguro/{id}',
        operationId: 'polizaSeguro_eliminar',
        summary: 'Enviar póliza a la papelera',
        description: 'Permiso requerido: `poliza_seguro_eliminar`.',
        tags: ['Pólizas de seguro'],
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
        path: '/api/poliza-seguro/{id}/restaurar',
        operationId: 'polizaSeguro_restaurar',
        summary: 'Restaurar póliza de la papelera',
        description: 'Permiso requerido: `poliza_seguro_restaurar`.',
        tags: ['Pólizas de seguro'],
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
