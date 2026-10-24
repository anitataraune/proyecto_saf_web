<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarCategoriaRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => 'sometimes|string|max:255|unique:categoria,nombre,'.$this->route('id').',id_categoria',
            'descripcion' => 'nullable|string',
            'vida_util'   => 'nullable|integer|min:1|max:100',
        ];
    }
}
