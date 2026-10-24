<?php

// Archivo generado a partir de routes/api.php y las FormRequests; puede editarse a mano.

namespace App\Swagger\Rutas;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Reportes', description: 'Reportes PDF de activos.')]
class ReporteDocs
{
    #[OA\Get(
        path: '/api/reporte/activo-general',
        operationId: 'reporte_reporteGeneral',
        summary: 'Reporte PDF general de activos',
        description: 'Permiso requerido: `reporte_activo_listar`.',
        tags: ['Reportes'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Archivo PDF', content: new OA\MediaType(mediaType: 'application/pdf', schema: new OA\Schema(type: 'string', format: 'binary'))),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
        ]
    )]
    public function reporteGeneral(): void {}

    #[OA\Get(
        path: '/api/reporte/activo-ubicacion',
        operationId: 'reporte_reportePorUbicacion',
        summary: 'Reporte PDF de activos por ubicación',
        description: 'Permiso requerido: `reporte_activo_listar`.',
        tags: ['Reportes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\QueryParameter(name: 'ubicacion_id', required: false, description: 'Filtrar por ubicación (vacío = todas)', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Archivo PDF', content: new OA\MediaType(mediaType: 'application/pdf', schema: new OA\Schema(type: 'string', format: 'binary'))),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
        ]
    )]
    public function reportePorUbicacion(): void {}

    #[OA\Get(
        path: '/api/reporte/activo-categoria',
        operationId: 'reporte_reportePorCategoria',
        summary: 'Reporte PDF de activos por categoría',
        description: 'Permiso requerido: `reporte_activo_listar`.',
        tags: ['Reportes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\QueryParameter(name: 'categoria_id', required: false, description: 'Filtrar por categoría (vacío = todas)', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Archivo PDF', content: new OA\MediaType(mediaType: 'application/pdf', schema: new OA\Schema(type: 'string', format: 'binary'))),
            new OA\Response(response: 401, ref: '#/components/responses/NoAutenticado'),
            new OA\Response(response: 403, ref: '#/components/responses/SinPermiso'),
        ]
    )]
    public function reportePorCategoria(): void {}
}
