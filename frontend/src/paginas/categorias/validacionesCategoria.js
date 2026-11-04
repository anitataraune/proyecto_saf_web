export const reglasCategoria = {
    nombre: {
        required: 'El nombre es obligatorio.',
        minLength: { 
            value: 3, 
            message: 'El nombre debe tener al menos 3 caracteres.' 
        },
    },
    descripcion: {
        // Opcional
    },
    vida_util: {
        min: { 
            value: 1, 
            message: 'La vida útil debe ser mayor o igual a 1 año.' 
        },
    },
};