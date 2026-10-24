<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarRolRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255|unique:roles,name,'.$this->route('id'),
            'permisos'   => 'nullable|array',
            'permisos.*' => 'string|exists:permissions,name',
        ];
    }
}
