<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PolizaSeguro;
use Illuminate\Http\Request;

class PolizaSeguroController extends Controller
{
    public function listar(Request $request)
    {
        $query = PolizaSeguro::query();
        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return response()->json($query->get(), 200);
    }

    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $poliza = PolizaSeguro::create($validado);

        return response()->json(['mensaje' => 'Póliza de seguro registrada', 'data' => $poliza], 201);
    }

    public function mostrar($id)
    {
        $poliza = PolizaSeguro::withTrashed()->findOrFail($id);

        return response()->json($poliza, 200);
    }

    public function actualizar(Request $request, $id)
    {
        $poliza = PolizaSeguro::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'descripcion' => 'sometimes|string',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'sometimes|date|after_or_equal:fecha_inicio',
        ]);

        $poliza->update($validado);

        return response()->json(['mensaje' => 'Póliza de seguro actualizada', 'data' => $poliza], 200);
    }

    public function eliminar($id)
    {
        $poliza = PolizaSeguro::findOrFail($id);
        $poliza->delete();

        return response()->json(['mensaje' => 'Póliza enviada a papelera'], 200);
    }

    public function restaurar($id)
    {
        $poliza = PolizaSeguro::onlyTrashed()->findOrFail($id);
        $poliza->restore();

        return response()->json(['mensaje' => 'Póliza restaurada con éxito'], 200);
    }
}
