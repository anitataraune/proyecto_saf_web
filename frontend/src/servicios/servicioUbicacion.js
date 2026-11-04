import api from '../api/axios';

export const servicioUbicacion = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/ubicacion', { params: { incluir_bajas: incluirBajas } });
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/ubicacion', datos);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/ubicacion/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/ubicacion/${id}`);
        return respuesta.data;
    },
    restaurar: async (id) => {
        const respuesta = await api.post(`/ubicacion/${id}/restaurar`);
        return respuesta.data;
    },
};
