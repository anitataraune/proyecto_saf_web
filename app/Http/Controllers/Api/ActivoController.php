<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarActivoRequest;
use App\Http\Requests\GuardarActivoRequest;
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

        return $this->responderLista($query, $request);
    }

    /**
     * Registrar un nuevo activo fijo en el sistema.
     */
    public function guardar(GuardarActivoRequest $request)
    {
        $validado = $request->validated();

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
    public function actualizar(ActualizarActivoRequest $request, $id)
    {
        $activo = Activo::findOrFail($id);
        $validado = $request->validated();

        // Los datos base del cálculo no pueden cambiar si el activo ya tiene depreciaciones
        $camposBase = ['valor_compra', 'fecha_compra', 'categoria_id'];
        $activo->fill($validado);

        // El estado lo controlan los módulos de Bajas y Depreciación, no la edición manual
        if ($activo->isDirty('estado_actual')) {
            return response()->json([
                'mensaje' => 'El estado del activo no se edita manualmente: use el módulo de Bajas o el proceso de Depreciación.'
            ], 422);
        }

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

        if ($activo->asignaciones()->where('estado_asignacion', 'asignado')->exists()) {
            return response()->json([
                'mensaje' => 'El activo tiene una asignación vigente; registre primero su devolución.'
            ], 422);
        }

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