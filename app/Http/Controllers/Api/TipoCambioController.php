<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoCambio;
use Illuminate\Http\Request;

class TipoCambioController extends Controller
{
    public function listar(Request $request)
    {
        $query = TipoCambio::query();
        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return response()->json($query->get(), 200);
    }

    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'ufvs' => 'required|numeric',
            'valor_tc' => 'required|numeric',
            'monto' => 'required|numerico',
        ]);

        $tipoCambio = TipoCambio::create($validado);

        return response()->json(['mensaje' => 'Tipo de cambio registrado', 'data' => $tipoCambio], 201);
    }

    public function mostrar($id)
    {
        $tipoCambio = TipoCambio::withTrashed()->findOrFail($id);

        return response()->json($tipoCambio, 200);
    }

    public function actualizar(Request $request, $id)
    {
        $tipoCambio = TipoCambio::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'ufvs' => 'sometimes|numeric',
            'valor_tc' => 'sometimes|numeric',
            'monto' => 'sometimes|numeric',
        ]);

        $tipoCambio->update($validado);

        return response()->json(['mensaje' => 'Tipo de cambio actualizado', 'data' => $tipoCambio], 200);
    }

    public function eliminar($id)
    {
        $tipoCambio = TipoCambio::findOrFail($id);
        $tipoCambio->delete();

        return response()->json(['mensaje' => 'Tipo de cambio enviado a papelera'], 200);
    }

    public function restaurar($id)
    {
        $tipoCambio = TipoCambio::onlyTrashed()->findOrFail($id);
        $tipoCambio->restore();

        return response()->json(['mensaje' => 'Tipo de cambio restaurado con éxito'], 200);
    }
}
