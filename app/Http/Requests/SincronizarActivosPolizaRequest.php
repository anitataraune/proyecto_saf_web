<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SincronizarActivosPolizaRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activos'          => 'nullable|array',
            'activos.*'        => 'integer|distinct|exists:activo,id_activo',
            'fecha_asignacion' => 'nullable|date',
        ];
    }
}
