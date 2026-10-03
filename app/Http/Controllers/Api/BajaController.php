<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\Baja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BajaController extends Controller
{
    public function listar(Request $request)
    {
        $query = Baja::with('activo:id_activo,descripcion,estado_actual');

        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return response()->json($query->get(), 200);
    }

    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'activo_id'   => 'required|exists:activo,id_activo',
            'fecha_baja'  => 'required|date',
            'motivo_baja' => 'required|string|max:255',
            'observacion' => 'nullable|string',
        ]);

        // Verificar que el activo no esté dado de baja previamente
        $activo = Activo::findOrFail($validado['activo_id']);
        if ($activo->estado_actual === 'dado_de_baja') {
            return response()->json([
                'mensaje' => 'El activo seleccionado ya se encuentra dado de baja.'
            ], 422);
        }

        // Transacción para garantizar consistencia
        $baja = DB::transaction(function () use ($validado, $activo) {
            // 1. Crear el registro de baja
            $registroBaja = Baja::create($validado);

            // 2. Actualizar el estado del activo
            $activo->update([
                'estado_actual' => 'dado_de_baja'
            ]);

            return $registroBaja;
        });

        return response()->json([
            'mensaje' => 'Registro de baja procesado con éxito.',
            'data'    => $baja->load('activo')
        ], 201);
    }

    public function mostrar($id)
    {
        $baja = Baja::withTrashed()->with('activo')->findOrFail($id);

        return response()->json($baja, 200);
    }

    public function actualizar(Request $request, $id)
    {
        $baja = Baja::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'fecha_baja'  => 'sometimes|date',
            'motivo_baja' => 'sometimes|string|max:255',
            'observacion' => 'nullable|string',
        ]);

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
            
            // Opcional: Si eliminas el registro de baja, restauras el estado del activo a 'activo'
            $activo = Activo::find($baja->activo_id);
            if ($activo) {
                $activo->update(['estado_actual' => 'activo']);
            }

            $baja->delete();
        });

        return response()->json(['mensaje' => 'Baja eliminada lógicamente'], 200);
    }

    public function restaurar($id)
    {
        DB::transaction(function () use ($id) {
            $baja = Baja::onlyTrashed()->findOrFail($id);
            $baja->restore();

            $activo = Activo::find($baja->activo_id);
            if ($activo) {
                $activo->update(['estado_actual' => 'dado_de_baja']);
            }
        });

        return response()->json(['mensaje' => 'Baja restaurada con éxito'], 200);
    }
}