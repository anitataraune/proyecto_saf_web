<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarUbicacionRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_ubicacion'      => 'required|string|max:255',
            'descripcion_ubicacion' => 'nullable|string',
        ];
    }
}
