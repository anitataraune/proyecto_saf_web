<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DevolucionAsignacionRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_devolucion' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
