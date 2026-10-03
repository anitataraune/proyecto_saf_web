<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Responsable;
use Illuminate\Http\Request;

class ResponsableController extends Controller
{
    public function listar(Request $request)
    {
        $query = Responsable::query();
        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return response()->json($query->get(), 200);
    }

    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'nombre_responsable' => 'required|string',
            'apellido_responsable' => 'required|string',
            'cargo_responsable' => 'required|string',
        ]);

        $responsable = Responsable::create($validado);

        return response()->json(['mensaje' => 'Responsable registrado', 'data' => $responsable], 201);
    }

    public function mostrar($id)
    {
        $responsable = Responsable::withTrashed()->findOrFail($id);

        return response()->json($responsable, 200);
    }

    public function actualizar(Request $request, $id)
    {
        $responsable = Responsable::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'nombre_responsable' => 'sometimes|string',
            'apellido_responsable' => 'sometimes|string',
            'cargo_responsable' => 'sometimes|string',
        ]);

        $responsable->update($validado);

        return response()->json(['mensaje' => 'Responsable actualizado', 'data' => $responsable], 200);
    }

    public function eliminar($id)
    {
        $responsable = Responsable::findOrFail($id);
        $responsable->delete();

        return response()->json(['mensaje' => 'Responsable enviado a papelera'], 200);
    }

    public function restaurar($id)
    {
        $responsable = Responsable::onlyTrashed()->findOrFail($id);
        $responsable->restore();

        return response()->json(['mensaje' => 'Responsable restaurado con éxito'], 200);
    }
}
