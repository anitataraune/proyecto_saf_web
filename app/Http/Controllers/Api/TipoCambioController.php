<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarTipoCambioRequest;
use App\Http\Requests\GuardarTipoCambioRequest;
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

        // Opcional: ordenar del más reciente al más antiguo
        $query->orderBy('fecha', 'desc');

        return $this->responderLista($query, $request);
    }

    public function guardar(GuardarTipoCambioRequest $request)
    {
        $validado = $request->validated();

        $tipoCambio = TipoCambio::create($validado);

        return response()->json(['mensaje' => 'Tipo de cambio registrado', 'data' => $tipoCambio], 201);
    }

    public function mostrar($id)
    {
        $tipoCambio = TipoCambio::withTrashed()->findOrFail($id);

        return response()->json($tipoCambio, 200);
    }

    public function actualizar(ActualizarTipoCambioRequest $request, $id)
    {
        $tipoCambio = TipoCambio::withTrashed()->findOrFail($id);

        $validado = $request->validated();

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