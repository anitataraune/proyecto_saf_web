<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisosTablasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la caché de Spatie para evitar conflictos de persistencia
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Estructura de permisos basada estrictamente en controladores y rutas API
        $permisosPorTabla = [
            'configuracion' => [
                'configuracion_actualizar'
            ],
            'activo' => [
                'activo_listar',
                'activo_guardar',
                'activo_mostrar',
                'activo_actualizar',
                'activo_eliminar',
                'activo_restaurar'
            ],
            'asignacion' => [
                'asignacion_listar',
                'asignacion_guardar',
                'asignacion_devolver',
                'asignacion_editar',
                'asignacion_eliminar',
                'asignacion_restaurar'
            ],
            'baja' => [
                'baja_listar',
                'baja_guardar',
                'baja_mostrar',
                'baja_actualizar',
                'baja_eliminar',
                'baja_restaurar'
            ],
            'categoria' => [
                'categoria_listar',
                'categoria_guardar',
                'categoria_mostrar',
                'categoria_actualizar',
                'categoria_eliminar',
                'categoria_restaurar'
            ],
            'depreciacion' => [
                'depreciacion_listar',
                'depreciacion_procesar',
                'depreciacion_eliminar',
                'depreciacion_restaurar'
            ],
            'poliza_seguro' => [
                'poliza_seguro_listar',
                'poliza_seguro_guardar',
                'poliza_seguro_mostrar',
                'poliza_seguro_actualizar',
                'poliza_seguro_eliminar',
                'poliza_seguro_restaurar'
            ],
            'proveedor' => [
                'proveedor_listar',
                'proveedor_guardar',
                'proveedor_mostrar',
                'proveedor_actualizar',
                'proveedor_eliminar',
                'proveedor_restaurar'
            ],
            'responsable' => [
                'responsable_listar',
                'responsable_guardar',
                'responsable_mostrar',
                'responsable_actualizar',
                'responsable_eliminar',
                'responsable_restaurar'
            ],
            'tipo_cambio' => [
                'tipo_cambio_listar',
                'tipo_cambio_guardar',
                'tipo_cambio_mostrar',
                'tipo_cambio_actualizar',
                'tipo_cambio_eliminar',
                'tipo_cambio_restaurar'
            ],
            'ubicacion' => [
                'ubicacion_listar',
                'ubicacion_guardar',
                'ubicacion_mostrar',
                'ubicacion_actualizar',
                'ubicacion_eliminar',
                'ubicacion_restaurar'
            ],
            'usuario' => [
                'usuario_listar',
                'usuario_guardar',
                'usuario_mostrar',
                'usuario_actualizar',
                'usuario_activar',
                'usuario_desactivar',
                'usuario_eliminar',
                'usuario_restaurar'
            ],
            'rol' => [
                'rol_listar',
                'rol_guardar',
                'rol_mostrar',
                'rol_actualizar',
                'rol_eliminar',
                'rol_listar_permisos'
            ],
            'usuario_rol' => [
                'usuario_rol_listar',
                'usuario_rol_guardar',
                'usuario_rol_mostrar',
                'usuario_rol_actualizar',
                'usuario_rol_eliminar'
            ],
        ];

        // Registramos todos los permisos usando el guard 'api'
        foreach ($permisosPorTabla as $tabla => $listaPermisos) {
            foreach ($listaPermisos as $permisoNombre) {
                Permission::findOrCreate($permisoNombre, 'api');
            }
        }

        $this->command->info('¡Todos los permisos han sido registrados correctamente!');
    }
}