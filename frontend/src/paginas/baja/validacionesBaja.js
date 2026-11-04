export const reglasBaja = {
    activo_id: {
        required: 'Debe seleccionar un activo.',
    },
    fecha_baja: {
        required: 'La fecha de baja es obligatoria.',
    },
    motivo_baja: {
        required: 'El motivo de baja es obligatorio.',
        minLength: {
            value: 3,
            message: 'El motivo debe tener al menos 3 caracteres.',
        },
        maxLength: {
            value: 255,
            message: 'El motivo no puede superar los 255 caracteres.',
        },
    },
    observacion: {
        // Opcional
    },
};