import api from '../api/axios';

export const servicioConfiguracion = {
    // Consulta pública de configuración
    obtener: async () => {
        const respuesta = await api.get('/configuracion');
        return respuesta.data;
    },

    // Actualización con soporte de archivos (multipart/form-data)
    actualizar: async (datos) => {
        const formData = new FormData();

        Object.keys(datos).forEach((key) => {
            if (datos[key] !== null && datos[key] !== undefined) {
                formData.append(key, datos[key]);
            }
        });

        const respuesta = await api.post('/configuracion', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        return respuesta.data;
    },
};