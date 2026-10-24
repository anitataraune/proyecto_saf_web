<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarProveedorRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'    => 'required|string|max:255',
            'apellido'  => 'nullable|string|max:255',
            'nit'       => 'nullable|string|max:50|unique:proveedor,nit',
            'direccion' => 'nullable|string|max:255',
        ];
    }
}
