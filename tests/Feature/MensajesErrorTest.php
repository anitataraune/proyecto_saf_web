<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MensajesErrorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function comoAdmin(): void
    {
        Sanctum::actingAs(Usuario::where('usuario', 'admin')->first());
    }

    public function test_validacion_en_espanol_con_nombres_legibles(): void
    {
        $this->comoAdmin();

        $this->postJson('/api/activo', ['valor_compra' => -5])
            ->assertStatus(422)
            ->assertJsonPath('errors.descripcion.0', 'El campo descripción es obligatorio.')
            ->assertJsonPath('errors.valor_compra.0', 'El campo valor de compra debe ser al menos 0.')
            ->assertJsonPath('mensaje', 'El campo descripción es obligatorio.');

        Categoria::create(['nombre' => 'EQUIPOS', 'vida_util' => 4]);
        $this->postJson('/api/categoria', ['nombre' => 'EQUIPOS'])
            ->assertJsonPath('errors.nombre.0', 'El valor de nombre ya está registrado.');

        $this->postJson('/api/configuracion', ['color_primario' => 'azul'])
            ->assertJsonPath('errors.color_primario.0', 'El color primario debe tener el formato #RRGGBB.');
    }

    public function test_sin_token_responde_401_en_espanol(): void
    {
        $this->getJson('/api/activo')
            ->assertUnauthorized()
            ->assertJsonPath('mensaje', 'Sesión no válida o expirada. Inicie sesión nuevamente.');
    }

    public function test_sin_permiso_responde_403_en_espanol(): void
    {
        Sanctum::actingAs(Usuario::create(['usuario' => 'ana', 'clave' => 'secreta123', 'activo' => true]));

        $this->getJson('/api/activo')
            ->assertForbidden()
            ->assertJsonPath('mensaje', 'No tiene permisos para realizar esta acción.');
    }

    public function test_registro_inexistente_y_ruta_inexistente_responden_404(): void
    {
        $this->comoAdmin();

        $this->getJson('/api/activo/99999')
            ->assertNotFound()
            ->assertJsonPath('mensaje', 'El registro solicitado no existe o fue eliminado.');

        $this->getJson('/api/no-existe')
            ->assertNotFound()
            ->assertJsonPath('mensaje', 'La ruta solicitada no existe.');
    }

    public function test_demasiados_intentos_responde_429_en_espanol(): void
    {
        foreach (range(1, 5) as $intento) {
            $this->postJson('/api/autenticacion/iniciar-sesion', ['usuario' => 'x', 'clave' => 'y']);
        }

        $this->postJson('/api/autenticacion/iniciar-sesion', ['usuario' => 'x', 'clave' => 'y'])
            ->assertStatus(429)
            ->assertJsonPath('mensaje', 'Demasiados intentos. Espere un momento e intente nuevamente.');
    }

    public function test_error_interno_no_expone_detalles_sin_debug(): void
    {
        config(['app.debug' => false]);
        Route::get('/api/prueba-error', fn () => throw new \RuntimeException('detalle interno secreto'));

        $this->getJson('/api/prueba-error')
            ->assertStatus(500)
            ->assertJsonPath('mensaje', 'Ocurrió un error interno en el servidor. Intente nuevamente.')
            ->assertDontSee('detalle interno secreto');
    }
}
