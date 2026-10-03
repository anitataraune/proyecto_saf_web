<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\Categoria;
use App\Models\Ubicacion;
use App\Models\Configuracion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    private function obtenerConfiguracion()
    {
        return Configuracion::first();
    }

    public function reporteGeneral()
    {
        // Cambiamos 'asignacionActual.empleado' por 'asignacionActual.responsable'
        $activos = Activo::with(['categoria', 'ubicacion', 'asignacionActual.responsable'])->get();
        $configuracion = $this->obtenerConfiguracion();

        $pdf = Pdf::loadView('reportes.general', compact('activos', 'configuracion'))
                  ->setPaper('letter', 'landscape');

        return $pdf->download('Reporte_General_Activos.pdf');
    }

    public function reportePorCategoria(Request $request)
    {
        $configuracion = $this->obtenerConfiguracion();

        if ($request->has('categoria_id') && !empty($request->categoria_id)) {
            // Cambiamos 'activos.asignacionActual.empleado' por 'activos.asignacionActual.responsable'
            $categorias = Categoria::where('id_categoria', $request->categoria_id)
                ->with(['activos.ubicacion', 'activos.asignacionActual.responsable'])
                ->get();
        } else {
            $categorias = Categoria::with(['activos.ubicacion', 'activos.asignacionActual.responsable'])->get();
        }

        $pdf = Pdf::loadView('reportes.por_categoria', compact('categorias', 'configuracion'))
                ->setPaper('letter', 'portrait');

        return $pdf->download('Reporte_Activos_Categoria.pdf');
    }

    public function reportePorUbicacion(Request $request)
    {
        $configuracion = $this->obtenerConfiguracion();

        if ($request->has('ubicacion_id') && !empty($request->ubicacion_id)) {
            // Cambiamos 'activos.asignacionActual.empleado' por 'activos.asignacionActual.responsable'
            $ubicaciones = Ubicacion::where('id_ubicacion', $request->ubicacion_id)
                ->with(['activos.categoria', 'activos.asignacionActual.responsable'])
                ->get();
        } else {
            $ubicaciones = Ubicacion::with(['activos.categoria', 'activos.asignacionActual.responsable'])->get();
        }

        $pdf = Pdf::loadView('reportes.por_ubicacion', compact('ubicaciones', 'configuracion'))
                  ->setPaper('letter', 'portrait');

        return $pdf->download('Reporte_Activos_Ubicacion.pdf');
    }
}