<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarBajaRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_baja'  => 'sometimes|date|before_or_equal:today',
            'motivo_baja' => 'sometimes|string|max:255',
            'observacion' => 'nullable|string',
        ];
    }
}
