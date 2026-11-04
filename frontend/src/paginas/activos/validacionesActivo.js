export const reglasActivo = {
    descripcion: {
        required: 'La descripción del activo es obligatoria.',
        maxLength: {
            value: 255,
            message: 'La descripción no puede superar los 255 caracteres.',
        },
    },
    valor_compra: {
        required: 'El valor de compra es obligatorio.',
        min: {
            value: 0,
            message: 'El valor no puede ser negativo.',
        },
    },
    fecha_compra: {
        required: 'La fecha de compra es obligatoria.',
    },
    categoria_id: {
        required: 'Debe seleccionar una categoría.',
    },
    ubicacion_id: {
        // Opcional
    },
    proveedor_id: {
        // Opcional
    },
    estado_actual: {
        // Opcional / Administrado en edición
    },
};