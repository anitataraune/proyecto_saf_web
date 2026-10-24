<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarCategoriaRequest;
use App\Http\Requests\GuardarCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function listar(Request $request)
    {
        $query = Categoria::query();

        // Si mandan ?incluir_bajas=true muestra activos y eliminados lógicos
        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return $this->responderLista($query, $request);
    }

    public function guardar(GuardarCategoriaRequest $request)
    {
        $validado = $request->validated();

        $categoria = Categoria::create($validado);

        return response()->json(['mensaje' => 'Categoría registrada', 'data' => $categoria], 201);
    }

    public function mostrar($id)
    {
        $categoria = Categoria::withTrashed()->findOrFail($id);

        return response()->json($categoria, 200);
    }

    public function actualizar(ActualizarCategoriaRequest $request, $id)
    {
        $categoria = Categoria::withTrashed()->findOrFail($id);

        $validado = $request->validated();

        $categoria->update($validado);

        return response()->json(['mensaje' => 'Categoría actualizada', 'data' => $categoria], 200);
    }

    // Soft Delete
    public function eliminar($id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->activos()->exists()) {
            return response()->json(['mensaje' => 'La categoría tiene activos registrados; reasígnelos antes de eliminarla.'], 422);
        }

        $categoria->delete();

        return response()->json(['mensaje' => 'Categoría enviada a papelera'], 200);
    }

    // Restaurar Soft Delete
    public function restaurar($id)
    {
        $categoria = Categoria::onlyTrashed()->findOrFail($id);
        $categoria->restore();

        return response()->json(['mensaje' => 'Categoría restaurada con éxito'], 200);
    }
}
