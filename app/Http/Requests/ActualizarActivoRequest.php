<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarActivoRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion'   => 'sometimes|required|string|max:255',
            'valor_compra'  => 'sometimes|required|numeric|min:0',
            'fecha_compra'  => 'sometimes|required|date|before_or_equal:today',
            'estado_actual' => 'sometimes|required|string|in:activo,depreciado,dado_de_baja',
            'categoria_id'  => 'sometimes|required|exists:categoria,id_categoria',
            'ubicacion_id'  => 'sometimes|nullable|exists:ubicacion,id_ubicacion',
            'proveedor_id'  => 'sometimes|nullable|exists:proveedor,id_proveedor',
        ];
    }
}
