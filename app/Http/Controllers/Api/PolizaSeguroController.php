<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PolizaSeguro;
use App\Models\Activo;
use Illuminate\Http\Request;

class PolizaSeguroController extends Controller
{
    /**
     * Listar todas las pólizas cargando sus activos asignados.
     */
    public function listar(Request $request)
    {
        $query = PolizaSeguro::with('activos:id_activo,descripcion');

        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Registrar una nueva póliza de seguro.
     */
    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'descripcion'  => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $poliza = PolizaSeguro::create($validado);

        return response()->json([
            'mensaje' => 'Póliza de seguro registrada con éxito', 
            'data'    => $poliza
        ], 201);
    }

    /**
     * Mostrar una póliza específica con sus activos asignados.
     */
    public function mostrar($id)
    {
        $poliza = PolizaSeguro::withTrashed()
            ->with('activos:id_activo,descripcion')
            ->findOrFail($id);

        return response()->json($poliza, 200);
    }

    /**
     * Actualizar los datos básicos de una póliza.
     */
    public function actualizar(Request $request, $id)
    {
        $poliza = PolizaSeguro::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'descripcion'  => 'sometimes|string',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin'    => 'sometimes|date|after_or_equal:fecha_inicio',
        ]);

        $poliza->update($validado);

        return response()->json([
            'mensaje' => 'Póliza de seguro actualizada con éxito', 
            'data'    => $poliza
        ], 200);
    }

    /**
     * Obtener el catálogo de activos agrupados por Categoría (para los acordeones en React).
     */
    public function catalogosActivosAgrupados()
    {
        // Traemos los activos que no están dados de baja
        $activos = Activo::with('categoria:id_categoria,nombre')
            ->where('estado_actual', '!=', 'dado_de_baja')
            ->get(['id_activo', 'descripcion', 'categoria_id']);

        // Agrupamos por el nombre de la categoría en mayúsculas
        $agrupados = $activos->groupBy(function ($activo) {
            return $activo->categoria ? mb_strtoupper($activo->categoria->nombre) : 'SIN CATEGORÍA';
        });

        return response()->json($agrupados, 200);
    }

    /**
     * Sincronizar los activos asignados a una póliza (Checkboxes en React).
     */
    public function sincronizarActivos(Request $request, $id)
    {
        $poliza = PolizaSeguro::findOrFail($id);

        $request->validate([
            'activos'          => 'nullable|array',
            'activos.*'        => 'exists:activo,id_activo',
            'fecha_asignacion' => 'nullable|date',
        ]);

        $idsActivos = $request->input('activos', []);
        // Si no se envía fecha_asignacion, usa por defecto la fecha_inicio de la póliza o el día actual
        $fechaAsignacion = $request->input('fecha_asignacion', $poliza->fecha_inicio?->format('Y-m-d') ?? now()->toDateString());

        // Preparamos la estructura [id_activo => ['fecha_asignacion' => 'YYYY-MM-DD']]
        $datosSincronizar = [];
        foreach ($idsActivos as $activoId) {
            $datosSincronizar[$activoId] = [
                'fecha_asignacion' => $fechaAsignacion,
            ];
        }

        // sync() agrega los nuevos, mantiene los existentes y elimina los que se desmarcaron
        $poliza->activos()->sync($datosSincronizar);

        return response()->json([
            'mensaje' => 'Activos asignados a la póliza correctamente.',
            'data'    => $poliza->load('activos:id_activo,descripcion')
        ], 200);
    }

    public function eliminar($id)
    {
        $poliza = PolizaSeguro::findOrFail($id);
        $poliza->delete();

        return response()->json(['mensaje' => 'Póliza enviada a la papelera'], 200);
    }

    public function restaurar($id)
    {
        $poliza = PolizaSeguro::onlyTrashed()->findOrFail($id);
        $poliza->restore();

        return response()->json(['mensaje' => 'Póliza restaurada con éxito'], 200);
    }
}