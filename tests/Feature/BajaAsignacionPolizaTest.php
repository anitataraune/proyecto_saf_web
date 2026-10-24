<?php

namespace Tests\Feature;

use App\Models\Activo;
use App\Models\Asignacion;
use App\Models\Baja;
use App\Models\Categoria;
use App\Models\Depreciacion;
use App\Models\PolizaSeguro;
use App\Models\Responsable;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BajaAsignacionPolizaTest extends TestCase
{
    use RefreshDatabase;

    private Activo $activo;

    private Responsable $responsable;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-24');
        $this->seed(DatabaseSeeder::class);
        Sanctum::actingAs(Usuario::where('usuario', 'admin')->first());

        $categoria = Categoria::create(['nombre' => 'PRUEBA', 'vida_util' => 1]);
        $this->activo = $this->crearActivo('Laptop', $categoria->id_categoria);
        $this->responsable = Responsable::create([
            'nombre_responsable' => 'Ana', 'apellido_responsable' => 'Pérez', 'cargo_responsable' => 'Contadora',
        ]);
    }

    private function crearActivo(string $descripcion, ?int $categoriaId = null): Activo
    {
        return Activo::create([
            'descripcion'  => $descripcion,
            'valor_compra' => 1200,
            'fecha_compra' => '2026-01-15',
            'categoria_id' => $categoriaId ?? $this->activo->categoria_id,
        ]);
    }

    private function asignar(string $fecha = '2026-03-01')
    {
        return $this->postJson('/api/asignacion', [
            'activo_id' => $this->activo->id_activo,
            'responsable_id' => $this->responsable->id_responsable,
            'fecha_asignacion' => $fecha,
        ]);
    }

    // ---------------- ASIGNACIONES ----------------

    public function test_devolucion_el_mismo_dia_de_la_asignacion(): void
    {
        $this->asignar('2026-03-01')->assertCreated();
        $asignacion = Asignacion::first();

        $this->postJson("/api/asignacion/{$asignacion->id_asignacion}/devolucion", ['fecha_devolucion' => '2026-03-01'])
            ->assertOk();
        $this->postJson('/api/asignacion', [
            'activo_id' => $this->activo->id_activo,
            'responsable_id' => $this->responsable->id_responsable,
            'fecha_asignacion' => '2026-03-02',
        ])->assertCreated();

        // Una devolución anterior a la asignación sigue rechazándose
        $segunda = Asignacion::where('estado_asignacion', 'asignado')->first();
        $this->postJson("/api/asignacion/{$segunda->id_asignacion}/devolucion", ['fecha_devolucion' => '2026-03-01'])
            ->assertStatus(422);
    }

    public function test_no_asigna_antes_de_la_compra_ni_a_responsable_eliminado(): void
    {
        $this->asignar('2026-01-01')->assertStatus(422);

        $this->responsable->delete();
        $this->asignar()->assertStatus(422)->assertJsonValidationErrors('responsable_id');
    }

    public function test_activos_disponibles_y_asignacion_actual_en_la_lista(): void
    {
        $libre = $this->crearActivo('Impresora');
        $deBaja = $this->crearActivo('Monitor');
        $deBaja->update(['estado_actual' => 'dado_de_baja']);
        $this->asignar()->assertCreated();

        $ids = collect($this->getJson('/api/activo/disponibles')->assertOk()->json())->pluck('id_activo')->all();
        $this->assertSame([$libre->id_activo], $ids);

        // La lista general incluye asignacion_actual (lo usa el filtro del frontend)
        $lista = collect($this->getJson('/api/activo')->json())->keyBy('id_activo');
        $this->assertSame('asignado', $lista[$this->activo->id_activo]['asignacion_actual']['estado_asignacion']);
        $this->assertNull($lista[$libre->id_activo]['asignacion_actual']);
    }

    public function test_restaurar_asignacion_no_duplica_asignaciones_vigentes(): void
    {
        $this->asignar()->assertCreated();
        $primera = Asignacion::first();
        $this->deleteJson("/api/asignacion/{$primera->id_asignacion}")->assertOk();

        // El activo quedó libre y se asigna a otra persona
        $this->asignar('2026-03-05')->assertCreated();

        $this->postJson("/api/asignacion/{$primera->id_asignacion}/restaurar")->assertStatus(422);
        $this->assertSame(1, Asignacion::where('estado_asignacion', 'asignado')->count());
    }

    // ---------------- BAJAS ----------------

    public function test_baja_cierra_la_asignacion_vigente_y_guarda_observacion(): void
    {
        $this->asignar()->assertCreated();

        $this->postJson('/api/baja', [
            'activo_id' => $this->activo->id_activo,
            'fecha_baja' => '2026-04-10',
            'motivo_baja' => 'Robo',
            'observacion' => 'Denuncia N° 123',
        ])->assertCreated()->assertJsonPath('asignacion_cerrada', true);

        $asignacion = Asignacion::first();
        $this->assertSame('devuelto', $asignacion->estado_asignacion);
        $this->assertSame('2026-04-10', $asignacion->fecha_devolucion->toDateString());
        $this->assertSame('Denuncia N° 123', Baja::first()->observacion);
        $this->assertSame('dado_de_baja', $this->activo->fresh()->estado_actual);
    }

    public function test_baja_valida_fechas(): void
    {
        $base = ['activo_id' => $this->activo->id_activo, 'motivo_baja' => 'Daño'];

        $this->postJson('/api/baja', $base + ['fecha_baja' => '2026-01-01'])->assertStatus(422);
        $this->postJson('/api/baja', $base + ['fecha_baja' => '2026-12-01'])->assertStatus(422);

        $this->asignar('2026-03-01');
        $this->postJson('/api/baja', $base + ['fecha_baja' => '2026-02-01'])->assertStatus(422);
        $this->assertSame('asignado', Asignacion::first()->estado_asignacion);
    }

    public function test_eliminar_baja_devuelve_estado_depreciado(): void
    {
        $this->activo->update(['estado_actual' => 'depreciado']);
        Depreciacion::create([
            'activo_id' => $this->activo->id_activo, 'mes' => 1, 'anio' => 2026, 'fecha_proceso' => '2026-01-31',
            'valor_inicial' => 1200, 'valor_actualizado' => 1200, 'depreciacion_periodo' => 1200,
            'depreciacion_acumulada' => 1200, 'valor_neto' => 0,
        ]);

        $this->postJson('/api/baja', [
            'activo_id' => $this->activo->id_activo, 'fecha_baja' => '2026-05-01', 'motivo_baja' => 'Obsoleto',
        ])->assertCreated();

        $baja = Baja::first();
        $this->deleteJson("/api/baja/{$baja->id_baja}")->assertOk();

        // Antes volvía siempre a 'activo'
        $this->assertSame('depreciado', $this->activo->fresh()->estado_actual);
    }

    public function test_restaurar_baja_con_asignacion_vigente_se_rechaza(): void
    {
        $this->postJson('/api/baja', [
            'activo_id' => $this->activo->id_activo, 'fecha_baja' => '2026-05-01', 'motivo_baja' => 'Error',
        ])->assertCreated();
        $baja = Baja::first();
        $this->deleteJson("/api/baja/{$baja->id_baja}")->assertOk();

        $this->asignar('2026-06-01')->assertCreated();

        $this->postJson("/api/baja/{$baja->id_baja}/restaurar")->assertStatus(422);
        $this->assertSame('activo', $this->activo->fresh()->estado_actual);
    }

    // ---------------- PÓLIZAS ----------------

    public function test_actualizar_poliza_con_una_sola_fecha(): void
    {
        $poliza = PolizaSeguro::create(['descripcion' => 'Todo riesgo', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-12-31']);

        // Antes fallaba siempre al enviar solo fecha_fin
        $this->putJson("/api/poliza-seguro/{$poliza->id_seguro}", ['fecha_fin' => '2027-06-30'])->assertOk();
        $this->putJson("/api/poliza-seguro/{$poliza->id_seguro}", ['fecha_inicio' => '2027-07-01'])->assertStatus(422);
        $this->putJson("/api/poliza-seguro/{$poliza->id_seguro}", ['fecha_fin' => '2025-12-31'])->assertStatus(422);
    }

    public function test_sincronizar_activos_conserva_fecha_original(): void
    {
        $poliza = PolizaSeguro::create(['descripcion' => 'Todo riesgo', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-12-31']);
        $otro = $this->crearActivo('Impresora');
        $url = "/api/poliza-seguro/{$poliza->id_seguro}/activos";

        $this->postJson($url, ['activos' => [$this->activo->id_activo], 'fecha_asignacion' => '2026-02-01'])->assertOk();
        $this->postJson($url, ['activos' => [$this->activo->id_activo, $otro->id_activo], 'fecha_asignacion' => '2026-05-01'])->assertOk();

        $fechas = DB::table('activo_poliza')->pluck('fecha_asignacion', 'activo_id');
        $this->assertSame('2026-02-01', $fechas[$this->activo->id_activo]);
        $this->assertSame('2026-05-01', $fechas[$otro->id_activo]);

        // Desmarcar quita el activo
        $this->postJson($url, ['activos' => [$otro->id_activo]])->assertOk();
        $this->assertSame([$otro->id_activo], DB::table('activo_poliza')->pluck('activo_id')->map('intval')->all());
    }

    public function test_sincronizar_rechaza_activos_de_baja_y_fechas_fuera_de_vigencia(): void
    {
        $poliza = PolizaSeguro::create(['descripcion' => 'Todo riesgo', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-12-31']);
        $url = "/api/poliza-seguro/{$poliza->id_seguro}/activos";

        $this->postJson($url, ['activos' => [$this->activo->id_activo], 'fecha_asignacion' => '2027-01-15'])->assertStatus(422);

        $this->activo->update(['estado_actual' => 'dado_de_baja']);
        $this->postJson($url, ['activos' => [$this->activo->id_activo]])->assertStatus(422);
        $this->assertSame(0, DB::table('activo_poliza')->count());
    }
}
