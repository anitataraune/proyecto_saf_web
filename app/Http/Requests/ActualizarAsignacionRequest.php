<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarAsignacionRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responsable_id'   => ['sometimes', 'integer', 'gt:0', Rule::exists('responsable', 'id_responsable')->whereNull('deleted_at')],
            'fecha_asignacion' => ['sometimes', 'date', 'before_or_equal:today'],
            'observacion'      => ['nullable', 'string', 'max:500'],
        ];
    }
}
