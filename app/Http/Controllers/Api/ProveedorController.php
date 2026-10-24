<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarProveedorRequest;
use App\Http\Requests\GuardarProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function listar(Request $request)
    {
        $query = Proveedor::query();
        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return $this->responderLista($query, $request);
    }

    public function guardar(GuardarProveedorRequest $request)
    {
        $validado = $request->validated();

        $proveedor = Proveedor::create($validado);

        return response()->json(['mensaje' => 'Proveedor registrado', 'data' => $proveedor], 201);
    }

    public function mostrar($id)
    {
        $proveedor = Proveedor::withTrashed()->findOrFail($id);

        return response()->json($proveedor, 200);
    }

    public function actualizar(ActualizarProveedorRequest $request, $id)
    {
        $proveedor = Proveedor::withTrashed()->findOrFail($id);

        $validado = $request->validated();

        $proveedor->update($validado);

        return response()->json(['mensaje' => 'Proveedor actualizado', 'data' => $proveedor], 200);
    }

    public function eliminar($id)
    {
        $proveedor = Proveedor::findOrFail($id);

        if ($proveedor->activos()->exists()) {
            return response()->json(['mensaje' => 'El proveedor tiene activos registrados; no puede eliminarse.'], 422);
        }

        $proveedor->delete();

        return response()->json(['mensaje' => 'Proveedor enviado a papelera'], 200);
    }

    public function restaurar($id)
    {
        $proveedor = Proveedor::onlyTrashed()->findOrFail($id);
        $proveedor->restore();

        return response()->json(['mensaje' => 'Proveedor restaurado con éxito'], 200);
    }
}
