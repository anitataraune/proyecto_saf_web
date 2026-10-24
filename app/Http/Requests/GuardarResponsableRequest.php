<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarResponsableRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_responsable'   => 'required|string|max:255',
            'apellido_responsable' => 'required|string|max:255',
            'cargo_responsable'    => 'required|string|max:255',
        ];
    }
}
