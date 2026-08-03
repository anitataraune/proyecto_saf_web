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
        $rolAdmin->givePermissionTo($permisoAccesoTotal);

        // 3. Crear el usuario administrador base usando las columnas del $fillable
        $usuarioAdmin = Usuario::create([
            'usuario' => 'admin',          // En lugar de email
            'clave' => '123456',         // el cast 'hashed' se encarga de encriptarla automáticamente
            'activo' => true,
        ]);

        // 4. Asignar el rol al usuario
        $usuarioAdmin->assignRole($rolAdmin);
    }
}
