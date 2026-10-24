<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Depreciación', description: 'Depreciación mensual en línea recta con actualización por UFV.')]
class DepreciacionDocs
{
    #[OA\Get(
        path: '/api/depreciacion',
        operationId: 'depreciacion_listar',
        summary: 'Listar depreciación',
        description: 'Permiso requerido: `depreciacion_listar`.',
        tags: ['Depreciación'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\QueryParameter(name: 'activo_id', required: false, description: 'Filtrar por activo', schema: new OA\Schema(type: 'integer')),
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
        path: '/api/depreciacion/procesar',
        operationId: 'depreciacion_procesarDepreciacion',
        summary: 'Procesar la depreciación de un mes',
        description: 'Procesa todos los activos vigentes para el mes indicado.

- Requiere el UFV de cierre del mes en Tipo de cambio.
- Los meses se procesan en orden, sin saltos, desde el mes de compra.
- Los activos que no se pueden procesar se informan en "omitidos" con su motivo.

Permiso requerido: `depreciacion_procesar`.',
        tags: ['Depreciación'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['mes', 'anio'],
                properties: [
                    new OA\Property(
                        property: 'mes',
                        type: 'integer',
                        minimum: 1,
                        maximum: 12,
                        example: 1
                    ),
                    new OA\Property(
                        property: 'anio',
                        type: 'integer',
                        minimum: 2000,
                        example: 2026
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
    public function procesarDepreciacion(): void {}

    #[OA\Delete(
        path: '/api/depreciacion/{id}',
        operationId: 'depreciacion_eliminar',
        summary: 'Enviar depreciación a la papelera',
        description: 'Solo se puede eliminar la última depreciación del activo; sus acumulados se recalculan.

Permiso requerido: `depreciacion_eliminar`.',
        tags: ['Depreciación'],
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
        path: '/api/depreciacion/{id}/restaurar',
        operationId: 'depreciacion_restaurar',
        summary: 'Restaurar depreciación de la papelera',
        description: 'Solo se restaura la depreciación del periodo siguiente a la última vigente.

Permiso requerido: `depreciacion_restaurar`.',
        tags: ['Depreciación'],
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
