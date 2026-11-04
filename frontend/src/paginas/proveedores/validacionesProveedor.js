export const reglasProveedor = {
    nombre: {
        required: 'El nombre o razón social es obligatorio.',
        minLength: {
            value: 2,
            message: 'Debe tener al menos 2 caracteres.',
        },
    },
    apellido: {
        // Opcional, sin validaciones requeridas estrictas
    },
    nit: {
        // Opcional, pero si se ingresa podemos validar longitud mínima opcional
        minLength: {
            value: 5,
            message: 'El NIT debe tener al menos 5 dígitos si se especifica.',
        },
    },
    direccion: {
        // Opcional
    },
};