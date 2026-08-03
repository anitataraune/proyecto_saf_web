<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\Asignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionController extends Controller
{
    public function listar(Request $request)
    {
        $query = Asignacion::with([
            'activo:id_activo,descripcion,estado_actual',
            'responsable:id_responsable,nombre_responsable,apellido_responsable,cargo_responsable'
        ]);

        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return response()->json($query->orderBy('id_asignacion', 'desc')->get(), 200);
    }

    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'activo_id'        => 'required|exists:activo,id_activo',
            'responsable_id'   => 'required|exists:responsable,id_responsable',
            'fecha_asignacion' => 'required|date',
            'observacion'      => 'nullable|string',
        ]);

        // Verificar que el activo no esté ya asignado activamente
        $asignadoActivo = Asignacion::where('activo_id', $validado['activo_id'])
            ->where('estado_asignacion', 'asignado')
            ->exists();

        if ($asignadoActivo) {
            return response()->json([
                'mensaje' => 'Este activo ya está asignado actualmente a otra persona.'
            ], 422);
        }

        // Validar que el activo esté 'activo' (no dado de baja)
        $activo = Activo::findOrFail($validado['activo_id']);
        if ($activo->estado_actual === 'dado_de_baja') {
            return response()->json([
                'mensaje' => 'No se puede asignar un activo que ha sido dado de baja.'
            ], 422);
        }

        $validado['estado_asignacion'] = 'asignado';
        $asignacion = Asignacion::create($validado);

        return response()->json([
            'mensaje' => 'Asignación registrada con éxito.',
            'data'    => $asignacion->load(['activo', 'responsable'])
        ], 201);
    }

    // Registrar la devolución del activo
    public function registrarDevolucion(Request $request, $id)
    {
        $request->validate([
            'fecha_devolucion' => 'required|date',
        ]);

        $asignacion = Asignacion::findOrFail($id);

        if ($asignacion->estado_asignacion === 'devuelto') {
            return response()->json(['mensaje' => 'Esta asignación ya fue devuelta previamente.'], 422);
        }

        $asignacion->update([
            'fecha_devolucion'  => $request->fecha_devolucion,
            'estado_asignacion' => 'devuelto',
        ]);

        return response()->json([
            'mensaje' => 'Devolución de activo registrada correctamente.',
            'data'    => $asignacion
        ], 200);
    }

    public function actualizar(Request $request, $id)
    {
        $asignacion = Asignacion::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'fecha_asignacion' => 'sometimes|date',
            'observacion'      => 'nullable|string',
        ]);

        $asignacion->update($validado);

        return response()->json(['mensaje' => 'Asignación actualizada', 'data' => $asignacion], 200);
    }

    public function eliminar($id)
    {
        $asignacion = Asignacion::findOrFail($id);
        $asignacion->delete();

        return response()->json(['mensaje' => 'Asignación enviada a papelera'], 200);
    }

    public function restaurar($id)
    {
        $asignacion = Asignacion::onlyTrashed()->findOrFail($id);
        $asignacion->restore();

        return response()->json(['mensaje' => 'Asignación restaurada'], 200);
    }
}