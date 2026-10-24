<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PolizaSeguro;
use App\Models\Activo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
            'fecha_fin'    => 'sometimes|date',
        ]);

        // Se comparan las fechas finales (las enviadas o las ya guardadas), así funciona aunque llegue solo una
        $fechaInicio = Carbon::parse($validado['fecha_inicio'] ?? $poliza->fecha_inicio);
        $fechaFin = Carbon::parse($validado['fecha_fin'] ?? $poliza->fecha_fin);

        if ($fechaFin->lt($fechaInicio)) {
            return response()->json([
                'mensaje' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'
            ], 422);
        }

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
            'activos.*'        => 'integer|distinct|exists:activo,id_activo',
            'fecha_asignacion' => 'nullable|date',
        ]);

        $idsActivos = array_map('intval', $request->input('activos', []));
        // Si no se envía fecha_asignacion, usa por defecto la fecha_inicio de la póliza o el día actual
        $fechaAsignacion = $request->input('fecha_asignacion', $poliza->fecha_inicio?->format('Y-m-d') ?? now()->toDateString());

        if (Carbon::parse($fechaAsignacion)->lt($poliza->fecha_inicio) || Carbon::parse($fechaAsignacion)->gt($poliza->fecha_fin)) {
            return response()->json([
                'mensaje' => 'La fecha de asignación debe estar dentro de la vigencia de la póliza.'
            ], 422);
        }

        $actuales = $poliza->activos()->withTrashed()->pluck('activo.id_activo')->map('intval')->all();
        $nuevos = array_values(array_diff($idsActivos, $actuales));
        $quitados = array_values(array_diff($actuales, $idsActivos));

        // Solo los activos que se agregan deben estar vigentes (los ya asegurados se conservan)
        $noValidos = Activo::withTrashed()
            ->whereIn('id_activo', $nuevos)
            ->where(fn ($q) => $q->whereNotNull('deleted_at')->orWhere('estado_actual', 'dado_de_baja'))
            ->pluck('descripcion');

        if ($noValidos->isNotEmpty()) {
            return response()->json([
                'mensaje' => 'No se pueden asegurar activos eliminados o dados de baja: '.$noValidos->implode(', ').'.'
            ], 422);
        }

        // Se agregan los nuevos y se quitan los desmarcados, sin tocar la fecha de los que ya estaban
        DB::transaction(function () use ($poliza, $nuevos, $quitados, $fechaAsignacion) {
            if ($quitados) {
                $poliza->activos()->detach($quitados);
            }

            if ($nuevos) {
                $poliza->activos()->attach(array_fill_keys($nuevos, ['fecha_asignacion' => $fechaAsignacion]));
            }
        });

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