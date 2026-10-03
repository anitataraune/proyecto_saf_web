<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\UsuarioRolController;
use App\Http\Controllers\Api\BajaController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\PolizaSeguroController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\ResponsableController;
use App\Http\Controllers\Api\TipoCambioController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\ActivoController;
use App\Http\Controllers\Api\AsignacionController; 
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\DepreciacionController;
use App\Http\Controllers\Api\ReporteController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------------
// RUTAS PÚBLICAS (Sin Token)
// -------------------------------------------------------------
Route::prefix('autenticacion')->group(function () {
    Route::post('iniciar-sesion', [AuthController::class, 'iniciarSesion']);
});

// Obtener colores, logo e identidad del sistema sin necesidad de estar logueado
Route::get('configuracion', [ConfiguracionController::class, 'obtenerConfiguracion']);

// -------------------------------------------------------------
// RUTAS PROTEGIDAS (Requieren Bearer Token)
// -------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Autenticación (Cerrar sesión solo requiere estar autenticado)
    Route::post(
        'autenticacion/cerrar-sesion',
        [AuthController::class, 'cerrarSesion']
    );

    // CONFIGURACIÓN DEL SISTEMA
    Route::prefix('configuracion')->group(function () {
        Route::post('/', [ConfiguracionController::class, 'actualizarConfiguracion'])->middleware('permission:configuracion_actualizar');
    });

    // USUARIOS
    Route::prefix('usuario')->group(function () {
        Route::get('/', [UsuarioController::class, 'listar'])->middleware('permission:usuario_listar');
        Route::post('/', [UsuarioController::class, 'guardar'])->middleware('permission:usuario_guardar');
        Route::get('{id}', [UsuarioController::class, 'mostrar'])->middleware('permission:usuario_mostrar');
        Route::put('{id}', [UsuarioController::class, 'actualizar'])->middleware('permission:usuario_actualizar');
        Route::delete('{id}', [UsuarioController::class, 'eliminar'])->middleware('permission:usuario_eliminar');
        Route::post('{id}/restaurar', [UsuarioController::class, 'restaurar'])->middleware('permission:usuario_restaurar');
        Route::patch('{id}/activar', [UsuarioController::class, 'activar'])->middleware('permission:usuario_activar');
        Route::patch('{id}/desactivar', [UsuarioController::class, 'desactivar'])->middleware('permission:usuario_desactivar');
    });

    // ROLES y PERMISOS
    Route::prefix('rol')->group(function () {
        Route::get('/', [RolController::class, 'listar'])->middleware('permission:rol_listar');
        Route::post('/', [RolController::class, 'guardar'])->middleware('permission:rol_guardar');
        Route::get('{id}', [RolController::class, 'mostrar'])->middleware('permission:rol_mostrar');
        Route::put('{id}', [RolController::class, 'actualizar'])->middleware('permission:rol_actualizar');
        Route::delete('{id}', [RolController::class, 'eliminar'])->middleware('permission:rol_eliminar');
        
        // Alimenta selects/checkboxes en el frontend
        Route::get('sistema/permisos', [RolController::class, 'listarPermisos'])->middleware('permission:rol_listar_permisos');
    });

    // ASIGNACIÓN DE ROLES A USUARIOS
    Route::prefix('usuario-rol')->group(function () {
        Route::get('/', [UsuarioRolController::class, 'listar'])->middleware('permission:usuario_rol_listar');
        Route::post('/', [UsuarioRolController::class, 'guardar'])->middleware('permission:usuario_rol_guardar');
        Route::get('{id}', [UsuarioRolController::class, 'mostrar'])->middleware('permission:usuario_rol_mostrar');
        Route::put('{id}', [UsuarioRolController::class, 'actualizar'])->middleware('permission:usuario_rol_actualizar');
        Route::delete('{id}', [UsuarioRolController::class, 'eliminar'])->middleware('permission:usuario_rol_eliminar');
    });

    // PROVEEDORES
    Route::prefix('proveedor')->group(function () {
        Route::get('/', [ProveedorController::class, 'listar'])->middleware('permission:proveedor_listar');
        Route::post('/', [ProveedorController::class, 'guardar'])->middleware('permission:proveedor_guardar');
        Route::get('{id}', [ProveedorController::class, 'mostrar'])->middleware('permission:proveedor_mostrar');
        Route::put('{id}', [ProveedorController::class, 'actualizar'])->middleware('permission:proveedor_actualizar');
        Route::delete('{id}', [ProveedorController::class, 'eliminar'])->middleware('permission:proveedor_eliminar');
        Route::post('{id}/restaurar', [ProveedorController::class, 'restaurar'])->middleware('permission:proveedor_restaurar');
    });

    // CATEGORÍAS
    Route::prefix('categoria')->group(function () {
        Route::get('/', [CategoriaController::class, 'listar'])->middleware('permission:categoria_listar');
        Route::post('/', [CategoriaController::class, 'guardar'])->middleware('permission:categoria_guardar');
        Route::get('{id}', [CategoriaController::class, 'mostrar'])->middleware('permission:categoria_mostrar');
        Route::put('{id}', [CategoriaController::class, 'actualizar'])->middleware('permission:categoria_actualizar');
        Route::delete('{id}', [CategoriaController::class, 'eliminar'])->middleware('permission:categoria_eliminar');
        Route::post('{id}/restaurar', [CategoriaController::class, 'restaurar'])->middleware('permission:categoria_restaurar');
    });

    // UBICACIONES
    Route::prefix('ubicacion')->group(function () {
        Route::get('/', [UbicacionController::class, 'listar'])->middleware('permission:ubicacion_listar');
        Route::post('/', [UbicacionController::class, 'guardar'])->middleware('permission:ubicacion_guardar');
        Route::get('{id}', [UbicacionController::class, 'mostrar'])->middleware('permission:ubicacion_mostrar');
        Route::put('{id}', [UbicacionController::class, 'actualizar'])->middleware('permission:ubicacion_actualizar');
        Route::delete('{id}', [UbicacionController::class, 'eliminar'])->middleware('permission:ubicacion_eliminar');
        Route::post('{id}/restaurar', [UbicacionController::class, 'restaurar'])->middleware('permission:ubicacion_restaurar');
    });

    // RESPONSABLES
    Route::prefix('responsable')->group(function () {
        Route::get('/', [ResponsableController::class, 'listar'])->middleware('permission:responsable_listar');
        Route::post('/', [ResponsableController::class, 'guardar'])->middleware('permission:responsable_guardar');
        Route::get('{id}', [ResponsableController::class, 'mostrar'])->middleware('permission:responsable_mostrar');
        Route::put('{id}', [ResponsableController::class, 'actualizar'])->middleware('permission:responsable_actualizar');
        Route::delete('{id}', [ResponsableController::class, 'eliminar'])->middleware('permission:responsable_eliminar');
        Route::post('{id}/restaurar', [ResponsableController::class, 'restaurar'])->middleware('permission:responsable_restaurar');
    });

    // ASIGNACIONES DE ACTIVOS A RESPONSABLES
    Route::prefix('asignacion')->group(function () {
        Route::get('/', [AsignacionController::class, 'listar'])->middleware('permission:asignacion_listar');
        Route::post('/', [AsignacionController::class, 'guardar'])->middleware('permission:asignacion_guardar');
        Route::get('{id}', [AsignacionController::class, 'mostrar'])->middleware('permission:asignacion_listar');
        Route::post('{id}/devolucion', [AsignacionController::class, 'registrarDevolucion'])->middleware('permission:asignacion_devolver');
        Route::put('{id}', [AsignacionController::class, 'actualizar'])->middleware('permission:asignacion_editar');
        Route::delete('{id}', [AsignacionController::class, 'eliminar'])->middleware('permission:asignacion_eliminar');
        Route::post('{id}/restaurar', [AsignacionController::class, 'restaurar'])->middleware('permission:asignacion_restaurar');
    });

    // PÓLIZAS DE SEGURO
    Route::prefix('poliza-seguro')->group(function () {
        Route::get('/', [PolizaSeguroController::class, 'listar'])->middleware('permission:poliza_seguro_listar');
        Route::get('activos-agrupados', [PolizaSeguroController::class, 'catalogosActivosAgrupados'])->middleware('permission:poliza_seguro_listar');
        Route::post('/', [PolizaSeguroController::class, 'guardar'])->middleware('permission:poliza_seguro_guardar');
        Route::get('{id}', [PolizaSeguroController::class, 'mostrar'])->middleware('permission:poliza_seguro_mostrar');
        Route::put('{id}', [PolizaSeguroController::class, 'actualizar'])->middleware('permission:poliza_seguro_actualizar');
        Route::post('{id}/activos', [PolizaSeguroController::class, 'sincronizarActivos'])->middleware('permission:poliza_seguro_actualizar');
        Route::delete('{id}', [PolizaSeguroController::class, 'eliminar'])->middleware('permission:poliza_seguro_eliminar');
        Route::post('{id}/restaurar', [PolizaSeguroController::class, 'restaurar'])->middleware('permission:poliza_seguro_restaurar');
    });

    // BAJAS
    Route::prefix('baja')->group(function () {
        Route::get('/', [BajaController::class, 'listar'])->middleware('permission:baja_listar');
        Route::post('/', [BajaController::class, 'guardar'])->middleware('permission:baja_guardar');
        Route::get('{id}', [BajaController::class, 'mostrar'])->middleware('permission:baja_mostrar');
        Route::put('{id}', [BajaController::class, 'actualizar'])->middleware('permission:baja_actualizar');
        Route::delete('{id}', [BajaController::class, 'eliminar'])->middleware('permission:baja_eliminar');
        Route::post('{id}/restaurar', [BajaController::class, 'restaurar'])->middleware('permission:baja_restaurar');
    });

    // TIPO DE CAMBIO
    Route::prefix('tipo-cambio')->group(function () {
        Route::get('/', [TipoCambioController::class, 'listar'])->middleware('permission:tipo_cambio_listar');
        Route::post('/', [TipoCambioController::class, 'guardar'])->middleware('permission:tipo_cambio_guardar');
        Route::get('{id}', [TipoCambioController::class, 'mostrar'])->middleware('permission:tipo_cambio_mostrar');
        Route::put('{id}', [TipoCambioController::class, 'actualizar'])->middleware('permission:tipo_cambio_actualizar');
        Route::delete('{id}', [TipoCambioController::class, 'eliminar'])->middleware('permission:tipo_cambio_eliminar');
        Route::post('{id}/restaurar', [TipoCambioController::class, 'restaurar'])->middleware('permission:tipo_cambio_restaurar');
    });

    // ACTIVOS
    Route::prefix('activo')->group(function () {
        Route::get('/disponibles', [ActivoController::class, 'listarDisponibles'])->middleware('permission:activo_listar');
        Route::get('/', [ActivoController::class, 'listar'])->middleware('permission:activo_listar');
        Route::post('/', [ActivoController::class, 'guardar'])->middleware('permission:activo_guardar');
        Route::get('{id}', [ActivoController::class, 'mostrar'])->middleware('permission:activo_mostrar');
        Route::put('{id}', [ActivoController::class, 'actualizar'])->middleware('permission:activo_actualizar');
        Route::delete('{id}', [ActivoController::class, 'eliminar'])->middleware('permission:activo_eliminar');
        Route::post('{id}/restaurar', [ActivoController::class, 'restaurar'])->middleware('permission:activo_restaurar');
    });

    // DEPRECIACIÓN
    Route::prefix('depreciacion')->group(function () {
        Route::get('/', [DepreciacionController::class, 'listar'])->middleware('permission:depreciacion_listar');
        Route::post('/procesar', [DepreciacionController::class, 'procesarDepreciacion'])->middleware('permission:depreciacion_procesar');
        Route::delete('{id}', [DepreciacionController::class, 'eliminar'])->middleware('permission:depreciacion_eliminar');
        Route::post('{id}/restaurar', [DepreciacionController::class, 'restaurar'])->middleware('permission:depreciacion_restaurar');
    });

    // REPORTES DE ACTIVOS FIJOS
    Route::prefix('reporte')->group(function () {
        Route::get('/activo-general', [ReporteController::class, 'reporteGeneral'])->middleware('permission:reporte_activo_listar');
        Route::get('/activo-ubicacion', [ReporteController::class, 'reportePorUbicacion'])->middleware('permission:reporte_activo_listar');
        Route::get('/activo-categoria', [ReporteController::class, 'reportePorCategoria'])->middleware('permission:reporte_activo_listar');
    });

});