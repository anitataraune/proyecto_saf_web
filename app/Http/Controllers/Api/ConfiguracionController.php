<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    /**
     * Obtener la configuración activa del sistema.
     * Si no existe ninguna, la crea con los valores por defecto de la migración.
     */
    public function obtenerConfiguracion()
    {
        $configuracion = Configuracion::firstOrCreate([]);

        if ($configuracion->logo) {
            $configuracion->logo_url = asset('storage/' . $configuracion->logo);
        } else {
            $configuracion->logo_url = null;
        }

        return response()->json($configuracion, 200);
    }

    /**
     * Actualizar la configuración del sistema.
     */
    public function actualizarConfiguracion(Request $request)
    {
        $request->validate([
            'nombre_sistema'   => 'nullable|string|max:255',
            'nombre_empresa'   => 'nullable|string|max:255',
            'nit_empresa'      => 'nullable|string|max:50',
            'direccion'        => 'nullable|string|max:255',
            'telefono'         => 'nullable|string|max:50',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'color_primario'   => 'nullable|string|size:7',
            'color_secundario' => 'nullable|string|size:7',
            'color_fondo'      => 'nullable|string|size:7',
            'color_superficie' => 'nullable|string|size:7',
            'color_texto'      => 'nullable|string|size:7',
            'fuente'           => 'nullable|string|max:100',
        ]);

        $configuracion = Configuracion::firstOrCreate([]);

        $datos = $request->only([
            'nombre_sistema',
            'nombre_empresa',
            'nit_empresa',
            'direccion',
            'telefono',
            'color_primario',
            'color_secundario',
            'color_fondo',
            'color_superficie',
            'color_texto',
            'fuente',
        ]);

        // Procesar subida del logo si se adjunta un archivo
        if ($request->hasFile('logo')) {
            if ($configuracion->logo && Storage::disk('public')->exists($configuracion->logo)) {
                Storage::disk('public')->delete($configuracion->logo);
            }

            $rutaLogo = $request->file('logo')->store('configuracion', 'public');
            $datos['logo'] = $rutaLogo;
        }

        $configuracion->update(array_filter($datos, fn($value) => !is_null($value)));

        if ($configuracion->logo) {
            $configuracion->logo_url = asset('storage/' . $configuracion->logo);
        }

        return response()->json([
            'mensaje' => 'Configuración actualizada correctamente.',
            'datos'   => $configuracion
        ], 200);
    }
}