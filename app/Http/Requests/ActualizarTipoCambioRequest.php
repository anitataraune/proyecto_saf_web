<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarTipoCambioRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha'    => 'sometimes|date|unique:tipo_cambio,fecha,'.$this->route('id').',id_tipo_cambio',
            'ufvs'     => 'sometimes|numeric|gt:0',
            'valor_tc' => 'sometimes|numeric|gt:0',
            'monto'    => 'sometimes|numeric|min:0',
        ];
    }
}
