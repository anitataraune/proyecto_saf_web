export const reglasResponsable = {
    nombre_responsable: {
        required: 'El nombre es obligatorio.',
        minLength: {
            value: 2,
            message: 'El nombre debe tener al menos 2 caracteres.',
        },
    },
    apellido_responsable: {
        required: 'El apellido es obligatorio.',
        minLength: {
            value: 2,
            message: 'El apellido debe tener al menos 2 caracteres.',
        },
    },
    cargo_responsable: {
        required: 'El cargo o puesto es obligatorio.',
        minLength: {
            value: 3,
            message: 'El cargo debe tener al menos 3 caracteres.',
        },
    },
};