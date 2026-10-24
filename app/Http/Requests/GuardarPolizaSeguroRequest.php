<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarPolizaSeguroRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion'  => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ];
    }
}
