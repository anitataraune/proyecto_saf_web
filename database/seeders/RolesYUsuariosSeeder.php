<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder; // 🚀 Importamos tu modelo personalizado
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesYUsuariosSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el permiso de acceso total usando el guard de la API
        $permisoAccesoTotal = Permission::findOrCreate('acceso_total', 'api');

        // 2. Crear el rol Administrador y asignarle ese permiso
        $rolAdmin = Role::findOrCreate('administrador', 'api');
        $permisos = Permission::where('guard_name', 'api')->get();
        $rolAdmin->syncPermissions($permisos); 
        
        // 3. Crear el usuario administrador base usando TUS columnas del $fillable
        // firstOrCreate permite volver a ejecutar el seeder sin error de clave única
        $usuarioAdmin = Usuario::firstOrCreate(
            ['usuario' => env('ADMIN_USUARIO', 'admin')],
            [
                'clave' => env('ADMIN_CLAVE', '123456'), // Tu cast 'hashed' se encarga de encriptarla automáticamente
                'activo' => true,
            ]
        );

        // 4. Asignar el rol al usuario
        $usuarioAdmin->assignRole($rolAdmin);
    }
}
