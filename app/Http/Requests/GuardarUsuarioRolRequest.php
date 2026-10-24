<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarUsuarioRolRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['required', Rule::exists('usuario', 'id_usuario')->whereNull('deleted_at')],
            'roles'      => 'required|array',
            'roles.*'    => 'string|exists:roles,name',
        ];
    }
}
