<?php

namespace Tests\Feature;

use App\Models\Activo;
use App\Models\Asignacion;
use App\Models\Categoria;
use App\Models\Responsable;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiGeneralTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    private Categoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-24');
        $this->seed(DatabaseSeeder::class);
        $this->admin = Usuario::where('usuario', 'admin')->first();
        $this->categoria = Categoria::create(['nombre' => 'PRUEBA', 'vida_util' => 5]);
    }

    private function crearActivo(array $datos = []): Activo
    {
        return Activo::create($datos + [
            'descripcion'  => 'Laptop',
            'valor_compra' => 1200,
            'fecha_compra' => '2026-01-15',
            'categoria_id' => $this->categoria->id_categoria,
        ]);
    }

    // ---------------- AUTENTICACIÓN Y PERMISOS ----------------

    public function test_login_correcto_e_incorrecto(): void
    {
        Usuario::create(['usuario' => 'ana', 'clave' => 'secreta123', 'activo' => true]);

        $this->postJson('/api/autenticacion/iniciar-sesion', ['usuario' => 'ana', 'clave' => 'secreta123'])
            ->assertOk()
            ->assertJsonStructure(['access_token', 'usuario' => ['id_usuario', 'roles', 'permisos']]);

        $this->postJson('/api/autenticacion/iniciar-sesion', ['usuario' => 'ana', 'clave' => 'otra'])->assertUnauthorized();
        $this->postJson('/api/autenticacion/iniciar-sesion', ['usuario' => 'nadie', 'clave' => 'otra'])->assertUnauthorized();
    }

    public function test_login_limita_intentos(): void
    {
        foreach (range(1, 5) as $intento) {
            $this->postJson('/api/autenticacion/iniciar-sesion', ['usuario' => 'x', 'clave' => 'y'])->assertUnauthorized();
        }

        $this->postJson('/api/autenticacion/iniciar-sesion', ['usuario' => 'x', 'clave' => 'y'])->assertStatus(429);
    }

    public function test_usuario_desactivado_pierde_acceso_aunque_tenga_token(): void
    {
        $usuario = Usuario::create(['usuario' => 'ana', 'clave' => 'secreta123', 'activo' => true]);
        $usuario->assignRole('administrador');
        $token = $usuario->createToken('prueba')->plainTextToken;

        $this->withToken($token)->getJson('/api/categoria')->assertOk();

        // Desactivación directa en la BD (sin pasar por el endpoint que revoca tokens)
        $usuario->update(['activo' => false]);
        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/categoria')->assertForbidden();
    }

    public function test_desactivar_revoca_tokens_y_no_permite_desactivarse_a_si_mismo(): void
    {
        $usuario = Usuario::create(['usuario' => 'ana', 'clave' => 'secreta123', 'activo' => true]);
        $usuario->createToken('prueba');

        Sanctum::actingAs($this->admin);
        $this->patchJson("/api/usuario/{$usuario->id_usuario}/desactivar")->assertOk();
        $this->assertSame(0, $usuario->tokens()->count());

        $this->patchJson("/api/usuario/{$this->admin->id_usuario}/desactivar")->assertStatus(422);
        $this->deleteJson("/api/usuario/{$this->admin->id_usuario}")->assertStatus(422);
    }

    public function test_sin_permiso_responde_403(): void
    {
        $rol = Role::create(['name' => 'consulta', 'guard_name' => 'api']);
        $rol->givePermissionTo('categoria_listar');
        $usuario = Usuario::create(['usuario' => 'ana', 'clave' => 'secreta123', 'activo' => true]);
        $usuario->assignRole($rol);

        Sanctum::actingAs($usuario);
        $this->getJson('/api/categoria')->assertOk();
        $this->postJson('/api/categoria', ['nombre' => 'NUEVA'])->assertForbidden();
    }

    public function test_rol_administrador_no_se_elimina(): void
    {
        Sanctum::actingAs($this->admin);
        $rol = Role::findByName('administrador', 'api');

        $this->deleteJson("/api/rol/{$rol->id}")->assertStatus(422);
    }

    // ---------------- PAGINACIÓN ----------------

    public function test_lista_completa_por_defecto_y_paginada_con_por_pagina(): void
    {
        Sanctum::actingAs($this->admin);
        foreach (range(1, 5) as $i) {
            $this->crearActivo(['descripcion' => "Activo $i"]);
        }

        // Sin parámetro: arreglo simple (compatible con el frontend actual)
        $this->getJson('/api/activo')->assertOk()->assertJsonCount(5);

        $this->getJson('/api/activo?por_pagina=2&page=3')
            ->assertOk()
            ->assertJsonPath('total', 5)
            ->assertJsonPath('last_page', 3)
            ->assertJsonPath('current_page', 3)
            ->assertJsonCount(1, 'data');
    }

    // ---------------- VALIDACIONES ----------------

    public function test_validaciones_de_activo(): void
    {
        Sanctum::actingAs($this->admin);
        $base = ['descripcion' => 'Silla', 'valor_compra' => 100, 'fecha_compra' => '2026-01-01', 'categoria_id' => $this->categoria->id_categoria];

        $this->postJson('/api/activo', ['valor_compra' => -5] + $base)->assertJsonValidationErrors('valor_compra');
        $this->postJson('/api/activo', ['fecha_compra' => '2027-01-01'] + $base)->assertJsonValidationErrors('fecha_compra');
        $this->postJson('/api/activo', ['estado_actual' => 'dado_de_baja'] + $base)->assertJsonValidationErrors('estado_actual');

        $eliminada = Categoria::create(['nombre' => 'VIEJA', 'vida_util' => 3]);
        $eliminada->delete();
        $this->postJson('/api/activo', ['categoria_id' => $eliminada->id_categoria] + $base)->assertJsonValidationErrors('categoria_id');

        $this->postJson('/api/activo', $base)->assertCreated();
    }

    public function test_estado_del_activo_no_se_edita_manualmente(): void
    {
        Sanctum::actingAs($this->admin);
        $activo = $this->crearActivo();

        // El frontend reenvía el estado actual en cada edición: eso sigue funcionando
        $this->putJson("/api/activo/{$activo->id_activo}", ['descripcion' => 'Laptop HP', 'estado_actual' => 'activo'])->assertOk();
        $this->putJson("/api/activo/{$activo->id_activo}", ['estado_actual' => 'dado_de_baja'])->assertStatus(422);
        $this->assertSame('activo', $activo->fresh()->estado_actual);
    }

    public function test_validaciones_de_catalogos(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/categoria', ['nombre' => 'X', 'vida_util' => 0])->assertJsonValidationErrors('vida_util');
        $this->postJson('/api/tipo-cambio', ['fecha' => '2026-01-31', 'ufvs' => 0, 'valor_tc' => 6.96, 'monto' => 0])
            ->assertJsonValidationErrors('ufvs');
        $this->postJson('/api/configuracion', ['color_primario' => 'azul123'])->assertJsonValidationErrors('color_primario');
        $this->postJson('/api/configuracion', ['color_primario' => '#1A2B3C'])->assertOk();
    }

    // ---------------- ELIMINACIÓN CON DEPENDENCIAS ----------------

    public function test_no_elimina_registros_con_dependencias_vigentes(): void
    {
        Sanctum::actingAs($this->admin);
        $activo = $this->crearActivo();
        $responsable = Responsable::create([
            'nombre_responsable' => 'Ana', 'apellido_responsable' => 'Pérez', 'cargo_responsable' => 'Contadora',
        ]);
        Asignacion::create([
            'activo_id' => $activo->id_activo, 'responsable_id' => $responsable->id_responsable,
            'fecha_asignacion' => '2026-02-01', 'estado_asignacion' => 'asignado',
        ]);

        $this->deleteJson("/api/categoria/{$this->categoria->id_categoria}")->assertStatus(422);
        $this->deleteJson("/api/responsable/{$responsable->id_responsable}")->assertStatus(422);
        $this->deleteJson("/api/activo/{$activo->id_activo}")->assertStatus(422);

        // Sin dependencias sí se elimina
        $libre = Categoria::create(['nombre' => 'LIBRE', 'vida_util' => 2]);
        $this->deleteJson("/api/categoria/{$libre->id_categoria}")->assertOk();
    }
}
