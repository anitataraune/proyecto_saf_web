<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarBajaRequest;
use App\Http\Requests\GuardarBajaRequest;
use App\Models\Activo;
use App\Models\Asignacion;
use App\Models\Baja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BajaController extends Controller
{
    public function listar(Request $request)
    {
        $query = Baja::with('activo:id_activo,descripcion,estado_actual');

        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return $this->responderLista($query, $request);
    }

    public function guardar(GuardarBajaRequest $request)
    {
        $validado = $request->validated();

        // Transacción para garantizar consistencia (el activo queda bloqueado mientras se procesa)
        return DB::transaction(function () use ($validado) {
            $activo = Activo::lockForUpdate()->findOrFail($validado['activo_id']);
            $fechaBaja = Carbon::parse($validado['fecha_baja']);

            // Verificar que el activo no esté dado de baja previamente
            if ($activo->estado_actual === 'dado_de_baja') {
                return response()->json([
                    'mensaje' => 'El activo seleccionado ya se encuentra dado de baja.'
                ], 422);
            }

            if ($fechaBaja->lt($activo->fecha_compra)) {
                return response()->json([
                    'mensaje' => 'La fecha de baja no puede ser anterior a la fecha de compra del activo.'
                ], 422);
            }

            // Un activo dado de baja no puede seguir asignado: se registra su devolución
            $asignacion = Asignacion::where('activo_id', $activo->id_activo)
                ->where('estado_asignacion', 'asignado')
                ->first();

            if ($asignacion && $fechaBaja->lt($asignacion->fecha_asignacion)) {
                return response()->json([
                    'mensaje' => 'La fecha de baja no puede ser anterior a la fecha de su asignación vigente.'
                ], 422);
            }

            if ($asignacion) {
                $asignacion->update([
                    'fecha_devolucion'  => $fechaBaja->toDateString(),
                    'estado_asignacion' => 'devuelto',
                ]);
            }

            $baja = Baja::create($validado);
            $activo->update(['estado_actual' => 'dado_de_baja']);

            return response()->json([
                'mensaje'            => 'Registro de baja procesado con éxito.',
                'asignacion_cerrada' => (bool) $asignacion,
                'data'               => $baja->load('activo')
            ], 201);
        });
    }

    public function mostrar($id)
    {
        $baja = Baja::withTrashed()->with('activo')->findOrFail($id);

        return response()->json($baja, 200);
    }

    public function actualizar(ActualizarBajaRequest $request, $id)
    {
        $baja = Baja::withTrashed()->with('activo')->findOrFail($id);

        $validado = $request->validated();

        if (isset($validado['fecha_baja']) && $baja->activo
            && Carbon::parse($validado['fecha_baja'])->lt($baja->activo->fecha_compra)) {
            return response()->json([
                'mensaje' => 'La fecha de baja no puede ser anterior a la fecha de compra del activo.'
            ], 422);
        }

        $baja->update($validado);

        return response()->json([
            'mensaje' => 'Registro de baja actualizado',
            'data'    => $baja
        ], 200);
    }

    public function eliminar($id)
    {
        DB::transaction(function () use ($id) {
            $baja = Baja::findOrFail($id);

            // Al anular la baja, el activo vuelve al estado que le corresponde según su depreciación
            $activo = Activo::lockForUpdate()->find($baja->activo_id);
            if ($activo) {
                $activo->update(['estado_actual' => $activo->estadoSegunDepreciacion()]);
            }

            $baja->delete();
        });

        return response()->json(['mensaje' => 'Baja eliminada lógicamente'], 200);
    }

    public function restaurar($id)
    {
        return DB::transaction(function () use ($id) {
            $baja = Baja::onlyTrashed()->findOrFail($id);
            $activo = Activo::lockForUpdate()->find($baja->activo_id);

            if (Baja::where('activo_id', $baja->activo_id)->exists()) {
                return response()->json([
                    'mensaje' => 'El activo ya tiene otro registro de baja vigente.'
                ], 422);
            }

            $tieneAsignacion = Asignacion::where('activo_id', $baja->activo_id)
                ->where('estado_asignacion', 'asignado')
                ->exists();

            if ($tieneAsignacion) {
                return response()->json([
                    'mensaje' => 'El activo tiene una asignación vigente; registre primero su devolución.'
                ], 422);
            }

            $baja->restore();

            if ($activo) {
                $activo->update(['estado_actual' => 'dado_de_baja']);
            }

            return response()->json(['mensaje' => 'Baja restaurada con éxito'], 200);
        });
    }
}
