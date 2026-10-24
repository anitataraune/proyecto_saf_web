<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarResponsableRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_responsable'   => 'sometimes|string|max:255',
            'apellido_responsable' => 'sometimes|string|max:255',
            'cargo_responsable'    => 'sometimes|string|max:255',
        ];
    }
}
