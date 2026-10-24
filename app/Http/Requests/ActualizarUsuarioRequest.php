<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarUsuarioRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario' => 'sometimes|string|max:255|unique:usuario,usuario,'.$this->route('id').',id_usuario',
            'clave'   => 'sometimes|string|min:6',
        ];
    }
}
