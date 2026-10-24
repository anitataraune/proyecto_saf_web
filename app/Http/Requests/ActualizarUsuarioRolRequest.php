<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarUsuarioRolRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roles'   => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }
}
