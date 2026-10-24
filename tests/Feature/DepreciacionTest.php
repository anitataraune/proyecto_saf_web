<?php

namespace Tests\Feature;

use App\Models\Activo;
use App\Models\Categoria;
use App\Models\Depreciacion;
use App\Models\TipoCambio;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepreciacionTest extends TestCase
{
    use RefreshDatabase;

    private Activo $activo;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-24');
        $this->seed(DatabaseSeeder::class);
        Sanctum::actingAs(Usuario::where('usuario', 'admin')->first());

        // Vida útil de 1 año = 12 meses
        $categoria = Categoria::create(['nombre' => 'PRUEBA', 'vida_util' => 1]);
        $this->activo = Activo::create([
            'descripcion'  => 'Laptop',
            'valor_compra' => 1200,
            'fecha_compra' => '2026-01-15',
            'categoria_id' => $categoria->id_categoria,
        ]);

        TipoCambio::create(['fecha' => '2026-01-10', 'ufvs' => 2.00, 'valor_tc' => 6.96, 'monto' => 0]);
        TipoCambio::create(['fecha' => '2026-01-31', 'ufvs' => 2.02, 'valor_tc' => 6.96, 'monto' => 0]);
        TipoCambio::create(['fecha' => '2026-02-28', 'ufvs' => 2.04, 'valor_tc' => 6.96, 'monto' => 0]);
    }

    private function procesar(int $mes, int $anio = 2026)
    {
        return $this->postJson('/api/depreciacion/procesar', ['mes' => $mes, 'anio' => $anio]);
    }

    public function test_calcula_depreciacion_con_actualizacion_ufv(): void
    {
        $this->procesar(1)->assertCreated()->assertJsonPath('total_procesados', 1);

        $dep = Depreciacion::first();
        // Factor 2.02 / 2.00 = 1.01
        $this->assertEquals(1200, $dep->valor_inicial);
        $this->assertEquals(12, $dep->actualizacion_valor);
        $this->assertEquals(1212, $dep->valor_actualizado);
        $this->assertEquals(101, $dep->depreciacion_periodo);
        $this->assertEquals(1111, $dep->valor_neto);

        $this->procesar(2)->assertCreated();

        $dep = Depreciacion::where('mes', 2)->first();
        // Factor 2.04 / 2.02; la depreciación acumulada anterior también se actualiza
        $factor = 2.04 / 2.02;
        $this->assertEquals(round(1212 * $factor, 2), $dep->valor_actualizado);
        $this->assertEquals(round(round(101 * $factor, 2) + round(1212 * $factor / 12, 2), 2), $dep->depreciacion_acumulada);

        $this->activo->refresh();
        $this->assertEquals(2, $this->activo->nro_depreciacion);
        $this->assertEquals($dep->depreciacion_acumulada, $this->activo->imp_depreciacion);
    }

    public function test_exige_ufv_del_periodo_y_bloquea_periodos_futuros(): void
    {
        $this->procesar(3)->assertStatus(422);
        $this->procesar(11)->assertStatus(422);
        $this->assertSame(0, Depreciacion::count());
    }

    public function test_no_duplica_ni_salta_periodos(): void
    {
        TipoCambio::create(['fecha' => '2026-03-31', 'ufvs' => 2.05, 'valor_tc' => 6.96, 'monto' => 0]);
        TipoCambio::create(['fecha' => '2025-12-31', 'ufvs' => 1.99, 'valor_tc' => 6.96, 'monto' => 0]);

        // Antes de la fecha de compra
        $this->procesar(12, 2025)->assertJsonPath('total_omitidos', 1);

        $this->procesar(1)->assertJsonPath('total_procesados', 1);
        $this->procesar(1)->assertJsonPath('total_omitidos', 1);
        // Saltar febrero
        $this->procesar(3)->assertJsonPath('total_omitidos', 1);

        $this->assertSame(1, Depreciacion::count());
    }

    public function test_eliminar_revierte_activo_y_permite_reprocesar(): void
    {
        $this->procesar(1);
        $this->procesar(2);
        $enero = Depreciacion::where('mes', 1)->first();
        $febrero = Depreciacion::where('mes', 2)->first();

        // Solo se puede eliminar la última
        $this->deleteJson("/api/depreciacion/{$enero->id_depreciacion}")->assertStatus(422);
        $this->deleteJson("/api/depreciacion/{$febrero->id_depreciacion}")->assertOk();

        $this->activo->refresh();
        $this->assertEquals(1, $this->activo->nro_depreciacion);
        $this->assertEquals($enero->depreciacion_acumulada, $this->activo->imp_depreciacion);

        // Reprocesar febrero no duplica (antes se sumaba dos veces)
        $this->procesar(2)->assertJsonPath('total_procesados', 1);
        $this->activo->refresh();
        $this->assertEquals(2, $this->activo->nro_depreciacion);
        $this->assertSame(2, Depreciacion::withTrashed()->count());
    }

    public function test_restaurar_respeta_el_orden(): void
    {
        $this->procesar(1);
        $this->procesar(2);
        $enero = Depreciacion::where('mes', 1)->first();
        $febrero = Depreciacion::where('mes', 2)->first();

        $this->deleteJson("/api/depreciacion/{$febrero->id_depreciacion}")->assertOk();
        $this->deleteJson("/api/depreciacion/{$enero->id_depreciacion}")->assertOk();

        $this->activo->refresh();
        $this->assertEquals(0, $this->activo->imp_depreciacion);

        $this->postJson("/api/depreciacion/{$febrero->id_depreciacion}/restaurar")->assertStatus(422);
        $this->postJson("/api/depreciacion/{$enero->id_depreciacion}/restaurar")->assertOk();
        $this->postJson("/api/depreciacion/{$febrero->id_depreciacion}/restaurar")->assertOk();

        $this->activo->refresh();
        $this->assertEquals($febrero->depreciacion_acumulada, $this->activo->imp_depreciacion);
    }

    public function test_activo_queda_depreciado_al_final_de_su_vida_util(): void
    {
        // Activo comprado en 2025 con UFV constante, para recorrer sus 12 meses de vida útil
        $activo = Activo::create([
            'descripcion'  => 'Impresora',
            'valor_compra' => 1000,
            'fecha_compra' => '2025-01-01',
            'categoria_id' => $this->activo->categoria_id,
        ]);

        foreach (range(1, 12) as $mes) {
            TipoCambio::create([
                'fecha' => Carbon::create(2025, $mes, 1)->endOfMonth()->toDateString(),
                'ufvs' => 2.0, 'valor_tc' => 6.96, 'monto' => 0,
            ]);
        }

        foreach (range(1, 12) as $mes) {
            $this->procesar($mes, 2025)->assertJsonPath('total_procesados', 1);
        }

        // 1000 / 12 = 83.33 por mes; el último mes cierra el redondeo
        $activo->refresh();
        $this->assertEquals(1000, $activo->imp_depreciacion);
        $this->assertSame('depreciado', $activo->estado_actual);
        $this->assertEquals(83.37, Depreciacion::where('activo_id', $activo->id_activo)->where('mes', 12)->value('depreciacion_periodo'));

        // Ya depreciado: no entra en el siguiente proceso
        TipoCambio::create(['fecha' => '2026-01-01', 'ufvs' => 2.0, 'valor_tc' => 6.96, 'monto' => 0]);
        $this->procesar(1, 2026);
        $this->assertSame(12, Depreciacion::where('activo_id', $activo->id_activo)->count());
    }

    public function test_no_permite_cambiar_valor_de_activo_depreciado(): void
    {
        $this->procesar(1);

        $this->putJson("/api/activo/{$this->activo->id_activo}", ['valor_compra' => 5000])->assertStatus(422);
        $this->putJson("/api/activo/{$this->activo->id_activo}", ['descripcion' => 'Laptop HP'])->assertOk();
    }
}
