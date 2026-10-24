<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarUsuarioRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario' => 'required|string|max:255|unique:usuario,usuario',
            'clave'   => 'required|string|min:6',
        ];
    }
}
