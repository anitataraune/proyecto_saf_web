<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarBajaRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activo_id'   => ['required', Rule::exists('activo', 'id_activo')->whereNull('deleted_at')],
            'fecha_baja'  => 'required|date|before_or_equal:today',
            'motivo_baja' => 'required|string|max:255',
            'observacion' => 'nullable|string',
        ];
    }
}
