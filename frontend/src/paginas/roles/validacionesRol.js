export const reglasRol = {
    name: {
        required: 'El nombre del rol es obligatorio.',
        minLength: {
            value: 3,
            message: 'El nombre del rol debe tener al menos 3 caracteres.',
        },
    },
    permisos: {
        // Los permisos son opcionales según tu controlador (nullable|array), 
        // pero puedes agregar reglas adicionales aquí si requieres que el rol tenga al menos 1 permiso de forma obligatoria.
    },
};