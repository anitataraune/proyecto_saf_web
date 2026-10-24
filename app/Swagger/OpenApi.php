<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'API Sistema de Activos Fijos',
    description: "API REST para la gestión de activos fijos: catálogos, asignaciones, bajas, pólizas, depreciación con UFV y reportes.\n\n"
        ."**Autenticación:** obtenga un token con `POST /api/autenticacion/iniciar-sesion` y presione **Authorize** para enviarlo como `Bearer`.\n\n"
        .'**Errores:** todas las respuestas de error incluyen la clave `mensaje` en español.'
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Servidor de la API')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Token devuelto por el inicio de sesión (sin el prefijo "Bearer").'
)]
#[OA\Schema(
    schema: 'Mensaje',
    properties: [
        new OA\Property(property: 'mensaje', type: 'string', example: 'Operación realizada con éxito.'),
        new OA\Property(property: 'message', type: 'string', description: 'Igual a mensaje (compatibilidad)'),
    ]
)]
#[OA\Schema(
    schema: 'ErrorValidacion',
    properties: [
        new OA\Property(property: 'mensaje', type: 'string', example: 'El campo descripción es obligatorio.'),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            description: 'Errores por campo (solo en errores de validación)',
            example: ['descripcion' => ['El campo descripción es obligatorio.']]
        ),
    ]
)]
#[OA\Response(response: 'NoAutenticado', description: 'Token ausente, inválido o expirado', content: new OA\JsonContent(ref: '#/components/schemas/Mensaje'))]
#[OA\Response(response: 'SinPermiso', description: 'El usuario no tiene el permiso requerido o está desactivado', content: new OA\JsonContent(ref: '#/components/schemas/Mensaje'))]
#[OA\Response(response: 'NoEncontrado', description: 'El registro no existe o fue eliminado', content: new OA\JsonContent(ref: '#/components/schemas/Mensaje'))]
#[OA\Response(response: 'ErrorValidacion', description: 'Datos inválidos o regla de negocio no cumplida', content: new OA\JsonContent(ref: '#/components/schemas/ErrorValidacion'))]
class OpenApi {}
