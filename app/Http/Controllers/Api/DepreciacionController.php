<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\Depreciacion;
use App\Models\TipoCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    // Procesa la depreciación masiva (línea recta con actualización por UFV) para un mes/año
    public function procesarDepreciacion(Request $request)
    {
        $validado = $request->validate([
            'mes'  => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2000',
        ]);

        $mes = (int) $validado['mes'];
        $anio = (int) $validado['anio'];

        $inicioMes = Carbon::create($anio, $mes, 1)->startOfDay();
        $finMes = $inicioMes->copy()->endOfMonth();

        if ($inicioMes->gt(now()->startOfMonth())) {
            return response()->json(['mensaje' => 'No se puede procesar un periodo futuro.'], 422);
        }

        // UFV de cierre del periodo: el último registrado dentro del mes
        $tipoCambioFin = TipoCambio::whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->orderByDesc('fecha')
            ->first();

        if (! $tipoCambioFin) {
            return response()->json([
                'mensaje' => 'Debe registrar el UFV del periodo '.$this->formatoPeriodo($mes, $anio).' en Tipo de Cambio antes de depreciar.',
            ], 422);
        }

        $resultado = DB::transaction(function () use ($mes, $anio, $tipoCambioFin) {
            $procesados = [];
            $omitidos = [];

            // Bloqueo de filas para evitar que dos procesos simultáneos dupliquen la depreciación
            $activos = Activo::with('categoria')
                ->where('estado_actual', 'activo')
                ->lockForUpdate()
                ->get();

            foreach ($activos as $activo) {
                $advertencia = null;
                $registro = $this->depreciarActivo($activo, $mes, $anio, $tipoCambioFin, $advertencia);

                if (is_string($registro)) {
                    $omitidos[] = ['activo_id' => $activo->id_activo, 'descripcion' => $activo->descripcion, 'motivo' => $registro];
                    continue;
                }

                if ($advertencia) {
                    $registro->setAttribute('advertencia', $advertencia);
                }

                $procesados[] = $registro;
            }

            return compact('procesados', 'omitidos');
        });

        return response()->json([
            'mensaje'          => 'Proceso de depreciación completado.',
            'total_procesados' => count($resultado['procesados']),
            'total_omitidos'   => count($resultado['omitidos']),
            'data'             => $resultado['procesados'],
            'omitidos'         => $resultado['omitidos'],
        ], 201);
    }

    /**
     * Calcula y registra la depreciación de un activo para el periodo indicado.
     * Devuelve el registro creado o un texto con el motivo por el que se omitió.
     */
    private function depreciarActivo(Activo $activo, int $mes, int $anio, TipoCambio $tipoCambioFin, ?string &$advertencia): Depreciacion|string
    {
        $periodo = $this->numeroPeriodo($mes, $anio);
        $fechaCompra = Carbon::parse($activo->fecha_compra);

        if ($periodo < $this->numeroPeriodo($fechaCompra->month, $fechaCompra->year)) {
            return 'El periodo es anterior a la fecha de compra.';
        }

        $vidaUtilMeses = (int) ($activo->categoria?->vida_util) * 12;
        if ($vidaUtilMeses <= 0) {
            return 'La categoría del activo no tiene una vida útil válida.';
        }

        $anterior = $this->ultimaDepreciacion($activo->id_activo);

        // Los periodos deben procesarse en orden y sin saltos
        if ($anterior) {
            $periodoAnterior = $this->numeroPeriodo($anterior->mes, $anterior->anio);

            if ($periodo <= $periodoAnterior) {
                return 'Ya tiene depreciación en este periodo o en uno posterior (último: '.$this->formatoPeriodo($anterior->mes, $anterior->anio).').';
            }

            if ($periodo !== $periodoAnterior + 1) {
                $siguiente = Carbon::create($anterior->anio, $anterior->mes, 1)->addMonth();

                return 'Falta procesar el periodo '.$this->formatoPeriodo($siguiente->month, $siguiente->year).'.';
            }
        }

        // UFV inicial: el de cierre de la depreciación anterior, o el vigente a la fecha de compra
        $ufvFin = (float) $tipoCambioFin->ufvs;
        $ufvInicio = $anterior?->ufv_fin
            ?? TipoCambio::where('fecha', '<=', $fechaCompra->toDateString())->orderByDesc('fecha')->value('ufvs');

        if (! $ufvInicio) {
            $ufvInicio = $ufvFin;
            $advertencia = 'No hay UFV registrado a la fecha de compra; no se aplicó actualización en este periodo.';
        }

        $ufvInicio = (float) $ufvInicio;
        $factor = $ufvFin / $ufvInicio;

        // Actualización del valor y de la depreciación acumulada por UFV
        $valorInicial = $anterior ? (float) $anterior->valor_actualizado : (float) $activo->valor_compra;
        $valorActualizado = round($valorInicial * $factor, 2);
        $actualizacionValor = round($valorActualizado - $valorInicial, 2);
        $depAcumuladaActualizada = round(($anterior ? (float) $anterior->depreciacion_acumulada : 0) * $factor, 2);

        // Línea recta sobre el valor actualizado; el último mes cierra la diferencia por redondeo
        $mesesDepreciados = Depreciacion::where('activo_id', $activo->id_activo)->count();
        $saldoPorDepreciar = round($valorActualizado - $depAcumuladaActualizada, 2);

        $depreciacionPeriodo = ($vidaUtilMeses - $mesesDepreciados) <= 1
            ? $saldoPorDepreciar
            : round($valorActualizado / $vidaUtilMeses, 2);
        $depreciacionPeriodo = max(0, min($depreciacionPeriodo, $saldoPorDepreciar));

        $depAcumulada = round($depAcumuladaActualizada + $depreciacionPeriodo, 2);
        $valorNeto = round($valorActualizado - $depAcumulada, 2);

        // Si el periodo fue procesado y luego eliminado, se descarta ese registro para reprocesarlo
        Depreciacion::onlyTrashed()
            ->where('activo_id', $activo->id_activo)
            ->where('mes', $mes)
            ->where('anio', $anio)
            ->forceDelete();

        $depreciacion = Depreciacion::create([
            'activo_id'              => $activo->id_activo,
            'tipo_cambio_id'         => $tipoCambioFin->id_tipo_cambio,
            'mes'                    => $mes,
            'anio'                   => $anio,
            'fecha_proceso'          => now()->toDateString(),
            'metodo_depreciacion'    => 'linea_recta',
            'ufv_inicio'             => $ufvInicio,
            'ufv_fin'                => $ufvFin,
            'valor_inicial'          => $valorInicial,
            'actualizacion_valor'    => $actualizacionValor,
            'valor_actualizado'      => $valorActualizado,
            'depreciacion_periodo'   => $depreciacionPeriodo,
            'depreciacion_acumulada' => $depAcumulada,
            'valor_neto'             => $valorNeto,
        ]);

        $this->sincronizarActivo($activo);

        return $depreciacion;
    }

    // Solo se permite eliminar la última depreciación del activo, para no romper la cadena de cálculos
    public function eliminar($id)
    {
        return DB::transaction(function () use ($id) {
            $dep = Depreciacion::lockForUpdate()->findOrFail($id);
            $ultima = $this->ultimaDepreciacion($dep->activo_id);

            if ($ultima && $ultima->id_depreciacion !== $dep->id_depreciacion) {
                return response()->json([
                    'mensaje' => 'Solo puede eliminarse la última depreciación del activo ('.$this->formatoPeriodo($ultima->mes, $ultima->anio).').',
                ], 422);
            }

            $dep->delete();

            if ($activo = Activo::withTrashed()->lockForUpdate()->find($dep->activo_id)) {
                $this->sincronizarActivo($activo);
            }

            return response()->json(['mensaje' => 'Registro de depreciación enviado a papelera.'], 200);
        });
    }

    // Solo se restaura si continúa la cadena: el periodo inmediato siguiente a la última depreciación vigente
    public function restaurar($id)
    {
        return DB::transaction(function () use ($id) {
            $dep = Depreciacion::onlyTrashed()->lockForUpdate()->findOrFail($id);
            $periodo = $this->numeroPeriodo($dep->mes, $dep->anio);
            $ultima = $this->ultimaDepreciacion($dep->activo_id);

            if ($ultima) {
                $esSiguiente = $periodo === $this->numeroPeriodo($ultima->mes, $ultima->anio) + 1;
            } else {
                $esSiguiente = ! Depreciacion::onlyTrashed()
                    ->where('activo_id', $dep->activo_id)
                    ->whereRaw('(anio * 12 + mes) < ?', [$periodo])
                    ->exists();
            }

            if (! $esSiguiente) {
                return response()->json([
                    'mensaje' => 'Solo puede restaurarse la depreciación del periodo siguiente a la última vigente; restaure primero las anteriores.',
                ], 422);
            }

            $dep->restore();

            if ($activo = Activo::withTrashed()->lockForUpdate()->find($dep->activo_id)) {
                $this->sincronizarActivo($activo);
            }

            return response()->json(['mensaje' => 'Registro de depreciación restaurado.'], 200);
        });
    }

    // Recalcula los acumulados del activo a partir de su última depreciación vigente
    private function sincronizarActivo(Activo $activo): void
    {
        $ultima = $this->ultimaDepreciacion($activo->id_activo);

        $datos = [
            'nro_depreciacion' => Depreciacion::where('activo_id', $activo->id_activo)->count(),
            'imp_depreciacion' => $ultima ? $ultima->depreciacion_acumulada : 0,
        ];

        // Un activo dado de baja conserva su estado
        if ($activo->estado_actual !== 'dado_de_baja') {
            $datos['estado_actual'] = ($ultima && $ultima->valor_neto <= 0) ? 'depreciado' : 'activo';
        }

        $activo->update($datos);
    }

    private function ultimaDepreciacion(int $activoId): ?Depreciacion
    {
        return Depreciacion::where('activo_id', $activoId)
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->first();
    }

    private function numeroPeriodo(int $mes, int $anio): int
    {
        return $anio * 12 + $mes;
    }

    private function formatoPeriodo(int $mes, int $anio): string
    {
        return sprintf('%02d/%d', $mes, $anio);
    }
}
