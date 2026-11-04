export const reglasUbicacion = {
    nombre_ubicacion: {
        required: 'El nombre de la ubicación es obligatorio.',
        minLength: { 
            value: 3, 
            message: 'El nombre debe tener al menos 3 caracteres.' 
        },
    },
    descripcion_ubicacion: {
        // Al ser opcional, solo validamos longitud máxima si deseas o dejamos libre
        maxLength: {
            value: 255,
            message: 'La descripción no puede superar los 255 caracteres.',
        },
    },
};