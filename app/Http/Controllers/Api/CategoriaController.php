<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        return response()->json($query->get(), 200);
    }

    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'nombre' => 'required|string|unique:categoria,nombre',
            'descripcion' => 'nullable|string',
            'vida_util' => 'nullable|integer',
        ]);

        $categoria = Categoria::create($validado);

        return response()->json(['mensaje' => 'Categoría registrada', 'data' => $categoria], 201);
    }

    public function mostrar($id)
    {
        $categoria = Categoria::withTrashed()->findOrFail($id);

        return response()->json($categoria, 200);
    }

    public function actualizar(Request $request, $id)
    {
        $categoria = Categoria::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'nombre' => 'sometimes|string|unique:categoria,nombre,'.$id.',id_categoria',
            'descripcion' => 'nullable|string',
            'vida_util' => 'nullable|integer',
        ]);

        $categoria->update($validado);

        return response()->json(['mensaje' => 'Categoría actualizada', 'data' => $categoria], 200);
    }

    // Soft Delete
    public function eliminar($id)
    {
        $categoria = Categoria::findOrFail($id);
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
