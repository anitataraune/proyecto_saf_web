export const obtenerReglasUsuario = (isEditMode) => ({
    usuario: {
        required: 'El nombre de usuario es obligatorio.',
        minLength: { 
            value: 3, 
            message: 'El usuario debe tener al menos 3 caracteres.' 
        },
    },
    clave: {
        required: {
            value: !isEditMode,
            message: 'La contraseña es obligatoria en nuevos usuarios.',
        },
        minLength: {
            value: 6, // Sincronizado con tu backend de Laravel (min:6)
            message: 'La contraseña debe tener al menos 6 caracteres.',
        },
    },
});