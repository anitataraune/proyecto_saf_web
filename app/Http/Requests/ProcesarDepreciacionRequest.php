<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcesarDepreciacionRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mes'  => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2000',
        ];
    }
}
