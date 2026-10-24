<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarCategoriaRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => 'required|string|max:255|unique:categoria,nombre',
            'descripcion' => 'nullable|string',
            // En años; debe ser positiva para poder depreciar
            'vida_util'   => 'nullable|integer|min:1|max:100',
        ];
    }
}
