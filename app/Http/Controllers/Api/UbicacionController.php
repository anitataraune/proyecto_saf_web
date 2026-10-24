<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarUbicacionRequest;
use App\Http\Requests\GuardarUbicacionRequest;
use App\Models\Ubicacion;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function listar(Request $request)
    {
        $query = Ubicacion::query();
        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return $this->responderLista($query, $request);
    }

    public function guardar(GuardarUbicacionRequest $request)
    {
        $validado = $request->validated();

        $ubicacion = Ubicacion::create($validado);

        return response()->json(['mensaje' => 'Ubicación registrada', 'data' => $ubicacion], 201);
    }

    public function mostrar($id)
    {
        $ubicacion = Ubicacion::withTrashed()->findOrFail($id);

        return response()->json($ubicacion, 200);
    }

    public function actualizar(ActualizarUbicacionRequest $request, $id)
    {
        $ubicacion = Ubicacion::withTrashed()->findOrFail($id);

        $validado = $request->validated();

        $ubicacion->update($validado);

        return response()->json(['mensaje' => 'Ubicación actualizada', 'data' => $ubicacion], 200);
    }

    public function eliminar($id)
    {
        $ubicacion = Ubicacion::findOrFail($id);

        if ($ubicacion->activos()->exists()) {
            return response()->json(['mensaje' => 'La ubicación tiene activos registrados; reasígnelos antes de eliminarla.'], 422);
        }

        $ubicacion->delete();

        return response()->json(['mensaje' => 'Ubicación enviada a papelera'], 200);
    }

    public function restaurar($id)
    {
        $ubicacion = Ubicacion::onlyTrashed()->findOrFail($id);
        $ubicacion->restore();

        return response()->json(['mensaje' => 'Ubicación restaurada con éxito'], 200);
    }
}
