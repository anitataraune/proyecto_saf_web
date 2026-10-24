<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarConfiguracionRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_sistema'   => 'nullable|string|max:255',
            'nombre_empresa'   => 'nullable|string|max:255',
            'nit_empresa'      => 'nullable|string|max:50',
            'direccion'        => 'nullable|string|max:255',
            'telefono'         => 'nullable|string|max:50',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'color_primario'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secundario' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_fondo'      => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_superficie' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_texto'      => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'fuente'           => 'nullable|string|max:100',
        ];
    }
}
