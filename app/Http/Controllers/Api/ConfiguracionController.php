<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarConfiguracionRequest;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    /**
     * Obtener la configuración activa del sistema.
     * Si no existe ninguna, la crea con los valores por defecto.
     */
    public function obtenerConfiguracion()
    {
        $configuracion = Configuracion::firstOrCreate([]);
        return response()->json($configuracion, 200);
    }

    /**
     * Actualizar la configuración del sistema.
     */
    public function actualizarConfiguracion(ActualizarConfiguracionRequest $request)
    {
        $configuracion = Configuracion::firstOrCreate([]);

        // Solo los campos validados (sin el archivo, que se procesa aparte)
        $datos = collect($request->validated())->except('logo')->all();

        // Procesar subida del logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($configuracion->logo && Storage::disk('public')->exists($configuracion->logo)) {
                Storage::disk('public')->delete($configuracion->logo);
            }

            $rutaLogo = $request->file('logo')->store('configuracion', 'public');
            $datos['logo'] = $rutaLogo;
        }

        $configuracion->update($datos);

        return response()->json([
            'mensaje' => 'Configuración actualizada correctamente.',
            'datos'   => $configuracion
        ], 200);
    }
}