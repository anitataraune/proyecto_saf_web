export const obtenerReglasUsuarioRol = (isEditMode) => ({
    usuario_id: {
        required: {
            value: !isEditMode,
            message: 'Debe seleccionar un usuario.',
        },
    },
    roles: {
        required: 'Seleccione al menos un rol para el usuario.',
        validate: (value) => 
            (Array.isArray(value) && value.length > 0) || 'Debe seleccionar al menos un rol.',
    },
});