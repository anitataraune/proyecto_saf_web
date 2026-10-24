<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarTipoCambioRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha'    => 'required|date|unique:tipo_cambio,fecha',
            'ufvs'     => 'required|numeric|gt:0',
            'valor_tc' => 'required|numeric|gt:0',
            'monto'    => 'required|numeric|min:0',
        ];
    }
}
