<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\Depreciacion;
use App\Models\TipoCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepreciacionController extends Controller
{
    public function listar(Request $request)
{
    // Carga de la relación asegurando que Eloquent pueda mapear 'activo_id' correctamente
    $query = Depreciacion::with(['activo' => function ($q) {
        $q->select('id_activo', 'descripcion', 'valor_compra', 'estado_actual');
    }]);

    // Usar filled() en lugar de has() para ignorar si mandan 'activo_id' vacío o nulo
    if ($request->filled('activo_id')) {
        $query->where('activo_id', $request->activo_id);
    }

    if ($request->boolean('incluir_bajas')) {
        $query->withTrashed();
    }

    // Retornar los resultados ordenados
    return response()->json(
        $query->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get(), 
        200
    );
}

    // Procesa la depreciación masiva para un mes/año específicos
    public function procesarDepreciacion(Request $request)
    {
        $validado = $request->validate([
            'mes'  => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2000',
        ]);

        $mes = $validado['mes'];
        $anio = $validado['anio'];

        // Obtener activos vigentes
        $activos = Activo::with('categoria')->where('estado_actual', 'activo')->get();

        if ($activos->isEmpty()) {
            return response()->json(['mensaje' => 'No hay activos vigentes para depreciar.'], 422);
        }

        $procesados = [];

        DB::transaction(function () use ($activos, $mes, $anio, &$procesados) {
            foreach ($activos as $activo) {
                // Verificar si ya se procesó este mes para este activo
                $existe = Depreciacion::where('activo_id', $activo->id_activo)
                    ->where('mes', $mes)
                    ->where('anio', $anio)
                    ->exists();

                if ($existe) continue;

                // Cálculo en Línea Recta según la vida útil de la categoría
                $vidaUtilAnios = $activo->categoria->vida_util ?? 5; // Default 5 años
                $vidaUtilMeses = $vidaUtilAnios * 12;
                
                $valorInicial = $activo->valor_compra - $activo->imp_depreciacion;
                $depreciacionMensual = round($activo->valor_compra / $vidaUtilMeses, 2);

                // No depreciar más allá del valor de compra
                $nuevaDepAcumulada = min($activo->valor_compra, $activo->imp_depreciacion + $depreciacionMensual);
                $depreciacionPeriodo = $nuevaDepAcumulada - $activo->imp_depreciacion;
                $valorNeto = $activo->valor_compra - $nuevaDepAcumulada;

                $depreciacion = Depreciacion::create([
                    'activo_id'            => $activo->id_activo,
                    'mes'                  => $mes,
                    'anio'                 => $anio,
                    'fecha_proceso'        => now()->toDateString(),
                    'metodo_depreciacion'  => 'linea_recta',
                    'valor_inicial'        => $valorInicial,
                    'actualizacion_valor'  => 0.00,
                    'valor_actualizado'    => $activo->valor_compra,
                    'depreciacion_periodo' => $depreciacionPeriodo,
                    'depreciacion_acumulada' => $nuevaDepAcumulada,
                    'valor_neto'           => $valorNeto,
                ]);

                // Actualizar contadores del activo
                $activo->increment('nro_depreciacion');
                $activo->update([
                    'imp_depreciacion' => $nuevaDepAcumulada,
                    'estado_actual'    => $valorNeto <= 0 ? 'depreciado' : 'activo',
                ]);

                $procesados[] = $depreciacion;
            }
        });

        return response()->json([
            'mensaje' => 'Proceso de depreciación completado.',
            'total_procesados' => count($procesados),
            'data' => $procesados,
        ], 201);
    }

    public function eliminar($id)
    {
        $dep = Depreciacion::findOrFail($id);
        $dep->delete();

        return response()->json(['mensaje' => 'Registro de depreciación enviado a papelera.'], 200);
    }

    public function restaurar($id)
    {
        $dep = Depreciacion::onlyTrashed()->findOrFail($id);
        $dep->restore();

        return response()->json(['mensaje' => 'Registro de depreciación restaurado.'], 200);
    }
}