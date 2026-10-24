<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarActivoRequest extends FormRequest
{
    // La autorización la resuelve el middleware de permisos de la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion'   => 'required|string|max:255',
            'valor_compra'  => 'required|numeric|min:0',
            'fecha_compra'  => 'required|date|before_or_equal:today',
            'categoria_id'  => ['required', Rule::exists('categoria', 'id_categoria')->whereNull('deleted_at')],
            'ubicacion_id'  => ['nullable', Rule::exists('ubicacion', 'id_ubicacion')->whereNull('deleted_at')],
            'proveedor_id'  => ['nullable', Rule::exists('proveedor', 'id_proveedor')->whereNull('deleted_at')],
            // Un activo nuevo siempre inicia vigente; la baja y la depreciación cambian su estado
            'estado_actual' => 'nullable|string|in:activo',
        ];
    }
}
