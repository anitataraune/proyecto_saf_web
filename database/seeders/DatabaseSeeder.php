<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Registramos la secuencia ordenada de ejecución de tus seeders
        $this->call([
            PermisosTablasSeeder::class,   // 1. Registra la lista de permisos granulares
            RolesYUsuariosSeeder::class,   // 2. Crea el permiso global, el rol y tu usuario admin
        ]);
    }
}