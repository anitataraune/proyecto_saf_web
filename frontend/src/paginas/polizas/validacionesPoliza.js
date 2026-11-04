export const reglasPoliza = {
    descripcion: {
        required: 'La descripción de la póliza es obligatoria.',
        minLength: {
            value: 4,
            message: 'La descripción debe tener al menos 4 caracteres.',
        },
    },
    fecha_inicio: {
        required: 'La fecha de inicio es obligatoria.',
    },
    fecha_fin: {
        required: 'La fecha de vencimiento es obligatoria.',
    },
};