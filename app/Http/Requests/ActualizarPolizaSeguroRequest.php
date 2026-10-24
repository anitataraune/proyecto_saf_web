<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarPolizaSeguroRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion'  => 'sometimes|string',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin'    => 'sometimes|date',
        ];
    }
}
