<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        return response()->json($query->get(), 200);
    }

    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'nombre_ubicacion' => 'required|string',
            'descripcion_ubicacion' => 'nullable|string',
        ]);

        $ubicacion = Ubicacion::create($validado);

        return response()->json(['mensaje' => 'Ubicación registrada', 'data' => $ubicacion], 201);
    }

    public function mostrar($id)
    {
        $ubicacion = Ubicacion::withTrashed()->findOrFail($id);

        return response()->json($ubicacion, 200);
    }

    public function actualizar(Request $request, $id)
    {
        $ubicacion = Ubicacion::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'nombre_ubicacion' => 'sometimes|string',
            'descripcion_ubicacion' => 'nullable|string',
        ]);

        $ubicacion->update($validado);

        return response()->json(['mensaje' => 'Ubicación actualizada', 'data' => $ubicacion], 200);
    }

    public function eliminar($id)
    {
        $ubicacion = Ubicacion::findOrFail($id);
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
