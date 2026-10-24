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

    public function listar(Request $request)
    {
        $query = Activo::with([
            'categoria:id_categoria,nombre', 
            'ubicacion:id_ubicacion,nombre_ubicacion',
            'proveedor:id_proveedor,nombre,apellido'
        ]);

        if ($request->has('eliminados') && $request->boolean('eliminados')) {
            $query->onlyTrashed();
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Registrar un nuevo activo fijo en el sistema.
     */
    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'descripcion' => 'required|string',
            'valor_compra' => 'required|numeric',
            'fecha_compra' => 'required|date',
            'categoria_id' => 'required|exists:categoria,id_categoria',
            'ubicacion_id' => 'nullable|exists:ubicacion,id_ubicacion',
            'proveedor_id' => 'nullable|exists:proveedor,id_proveedor', // Validación añadida
            'estado_actual' => 'nullable|string',
        ]);

        $activo = Activo::create($validado);
        return response()->json(['mensaje' => 'Activo registrado', 'activo' => $activo], 201);
    }

    /**
     * Mostrar los detalles de un activo específico.
     */
    public function mostrar($id)
    {
        // Cargamos la relación de ubicación
        $activo = Activo::with(['categoria', 'ubicacion'])->findOrFail($id);
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
            'ubicacion_id'  => 'sometimes|nullable|exists:ubicacion,id_ubicacion', // <--- Validación
            'proveedor_id'  => 'sometimes|nullable|exists:proveedor,id_proveedor',
        ];

        // Solo se actualizan los campos validados (evita modificar imp_depreciacion, nro_depreciacion, etc.)
        $validado = $request->validate($reglas);

        // Los datos base del cálculo no pueden cambiar si el activo ya tiene depreciaciones
        $camposBase = ['valor_compra', 'fecha_compra', 'categoria_id'];
        $activo->fill($validado);
        if ($activo->isDirty($camposBase) && $activo->depreciaciones()->exists()) {
            return response()->json([
                'mensaje' => 'No se puede modificar el valor, la fecha de compra ni la categoría de un activo con depreciaciones registradas.'
            ], 422);
        }

        $activo->save();

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