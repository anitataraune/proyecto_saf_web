<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use Illuminate\Http\Request;

class ActivoController extends Controller
{
    /**
     * Listar todos los activos con su categoría correspondiente.
     */
    public function listar()
    {
        // Traemos los activos incluyendo los datos de su categoría unida
        $activos = Activo::with('categoria:id_categoria,nombre')->get();
        return response()->json($activos, 200);
    }

    public function listarDisponibles()
    {
        $activos = Activo::where('estado_actual', '!=', 'dado_de_baja')
                        ->select('id_activo', 'descripcion', 'estado_actual')
                        ->get();

        return response()->json($activos, 200);
    }

    /**
     * Registrar un nuevo activo fijo en el sistema.
     */
    public function guardar(Request $request)
    {
        $reglas = [
            'descripcion'  => 'required|string|max:255',
            'valor_compra' => 'required|numeric|min:0',
            'fecha_compra' => 'required|date',
            'categoria_id' => 'required|exists:categoria,id_categoria',
        ];

        $request->validate($reglas);

        // Los campos de depreciación y estado toman sus valores por defecto de la migración
        $activo = Activo::create($request->all());

        return response()->json([
            'mensaje' => 'Activo registrado.',
            'activo'  => $activo
        ], 201);
    }

    /**
     * Mostrar los detalles de un activo específico.
     */
    public function mostrar($id)
    {
        $activo = Activo::with('categoria')->findOrFail($id);
        return response()->json($activo, 200);
    }

    /**
     * Actualizar los datos de un activo existente.
     */
    public function actualizar(Request $request, $id)
    {
        $activo = Activo::findOrFail($id);

        $reglas = [
            'descripcion'   => 'sometimes|required|string|max:255',
            'valor_compra'  => 'sometimes|required|numeric|min:0',
            'fecha_compra'  => 'sometimes|required|date',
            'estado_actual' => 'sometimes|required|string|in:activo,depreciado,dado_de_baja',
            'categoria_id'  => 'sometimes|required|exists:categoria,id_categoria',
        ];

        $request->validate($reglas);
        $activo->update($request->all());

        return response()->json([
            'mensaje' => 'Activo actualizado con éxito.',
            'activo'  => $activo
        ], 200);
    }

    /**
     * Eliminación lógica (Soft Delete) de un activo.
     */
    public function eliminar($id)
    {
        $activo = Activo::findOrFail($id);
        $activo->delete();

        return response()->json([
            'mensaje' => 'Activo enviado a la papelera con éxito.'
        ], 200);
    }

    /**
     * Restaurar un activo eliminado lógicamente.
     */
    public function restaurar($id)
    {
        // Buscamos únicamente en los registros que sufrieron Soft Delete
        $activo = Activo::onlyTrashed()->findOrFail($id);
        $activo->restore();

        return response()->json([
            'mensaje' => 'Activo restaurado con éxito.',
            'activo'  => $activo
        ], 200);
    }
}