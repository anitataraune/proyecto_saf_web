<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarAsignacionRequest;
use App\Http\Requests\DevolucionAsignacionRequest;
use App\Http\Requests\GuardarAsignacionRequest;
use App\Models\Activo;
use App\Models\Asignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AsignacionController extends Controller
{
    private function autorizarAccion(string $permiso): void
    {
        if (! auth()->check() || ! auth()->user()->can($permiso)) {
            abort(403, 'No tiene permisos para realizar esta acción.');
        }
    }

    private function limpiarTexto(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $valor = trim($valor);
        $valor = strip_tags($valor);
        $valor = preg_replace('/\s+/', ' ', $valor);

        return $valor !== null ? trim($valor) : null;
    }

    public function listar(Request $request)
    {
        $this->autorizarAccion('asignacion_listar');

        $query = Asignacion::with([
            'activo:id_activo,descripcion,estado_actual',
            'responsable:id_responsable,nombre_responsable,apellido_responsable,cargo_responsable'
        ]);

        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return $this->responderLista($query->orderBy('id_asignacion', 'desc'), $request);
    }

    public function guardar(GuardarAsignacionRequest $request)
    {
        $this->autorizarAccion('asignacion_guardar');

        $validado = $request->validated();

        $validado['observacion'] = $this->limpiarTexto($request->input('observacion'));

        // El bloqueo del activo evita que dos peticiones simultáneas lo asignen dos veces
        return DB::transaction(function () use ($validado) {
            $activo = Activo::lockForUpdate()->findOrFail($validado['activo_id']);

            $asignadoActivo = Asignacion::where('activo_id', $validado['activo_id'])
                ->where('estado_asignacion', 'asignado')
                ->exists();

            if ($asignadoActivo) {
                return response()->json([
                    'mensaje' => 'Este activo ya está asignado actualmente a otra persona.'
                ], 422);
            }

            if ($activo->estado_actual === 'dado_de_baja') {
                return response()->json([
                    'mensaje' => 'No se puede asignar un activo que ha sido dado de baja.'
                ], 422);
            }

            if (Carbon::parse($validado['fecha_asignacion'])->lt($activo->fecha_compra)) {
                return response()->json([
                    'mensaje' => 'La fecha de asignación no puede ser anterior a la fecha de compra del activo.'
                ], 422);
            }

            $validado['estado_asignacion'] = 'asignado';
            $asignacion = Asignacion::create($validado);

            return response()->json([
                'mensaje' => 'Asignación registrada con éxito.',
                'data'    => $asignacion->load(['activo', 'responsable'])
            ], 201);
        });
    }

    public function mostrar($id)
    {
        $this->autorizarAccion('asignacion_listar');

        $asignacion = Asignacion::with([
            'activo:id_activo,descripcion,estado_actual',
            'responsable:id_responsable,nombre_responsable,apellido_responsable,cargo_responsable'
        ])->withTrashed()->findOrFail($id);

        return response()->json($asignacion, 200);
    }

    public function registrarDevolucion(DevolucionAsignacionRequest $request, $id)
    {
        $this->autorizarAccion('asignacion_devolver');

        $asignacion = Asignacion::findOrFail($id);

        if ($asignacion->estado_asignacion === 'devuelto') {
            return response()->json(['mensaje' => 'Esta asignación ya fue devuelta previamente.'], 422);
        }

        if (Carbon::parse($request->fecha_devolucion)->lt($asignacion->fecha_asignacion)) {
            return response()->json([
                'mensaje' => 'La fecha de devolución no puede ser anterior a la fecha de asignación.'
            ], 422);
        }

        $asignacion->update([
            'fecha_devolucion'  => $request->fecha_devolucion,
            'estado_asignacion' => 'devuelto',
        ]);

        return response()->json([
            'mensaje' => 'Devolución de activo registrada correctamente.',
            'data'    => $asignacion->fresh()
        ], 200);
    }

    public function actualizar(ActualizarAsignacionRequest $request, $id)
    {
        $this->autorizarAccion('asignacion_editar');

        $asignacion = Asignacion::findOrFail($id);

        $validado = $request->validated();

        if (isset($validado['observacion'])) {
            $validado['observacion'] = $this->limpiarTexto($validado['observacion']);
        }

        if (isset($validado['fecha_asignacion']) && isset($asignacion->fecha_devolucion)
            && Carbon::parse($validado['fecha_asignacion'])->gt($asignacion->fecha_devolucion)) {
            return response()->json([
                'mensaje' => 'La fecha de asignación no puede ser posterior a la fecha de devolución.'
            ], 422);
        }

        $asignacion->update($validado);

        return response()->json([
            'mensaje' => 'Asignación actualizada',
            'data'    => $asignacion->load(['activo', 'responsable'])
        ], 200);
    }

    public function eliminar($id)
    {
        $this->autorizarAccion('asignacion_eliminar');

        $asignacion = Asignacion::findOrFail($id);
        $asignacion->delete();

        return response()->json(['mensaje' => 'Asignación enviada a papelera'], 200);
    }

    public function restaurar($id)
    {
        $this->autorizarAccion('asignacion_restaurar');

        $asignacion = Asignacion::onlyTrashed()->with('activo')->findOrFail($id);

        // Una asignación vigente solo se restaura si el activo sigue disponible
        if ($asignacion->estado_asignacion === 'asignado') {
            $otraVigente = Asignacion::where('activo_id', $asignacion->activo_id)
                ->where('estado_asignacion', 'asignado')
                ->exists();

            if ($otraVigente) {
                return response()->json(['mensaje' => 'El activo ya tiene otra asignación vigente.'], 422);
            }

            if (! $asignacion->activo || $asignacion->activo->estado_actual === 'dado_de_baja') {
                return response()->json(['mensaje' => 'El activo fue eliminado o dado de baja; no se puede restaurar la asignación.'], 422);
            }
        }

        $asignacion->restore();

        return response()->json(['mensaje' => 'Asignación restaurada'], 200);
    }
}