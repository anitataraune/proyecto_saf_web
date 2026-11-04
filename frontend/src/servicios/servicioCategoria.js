import api from '../api/axios';

export const servicioCategoria = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/categoria', { params: { incluir_bajas: incluirBajas } });
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/categoria', datos);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/categoria/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/categoria/${id}`);
        return respuesta.data;
    },
    restaurar: async (id) => {
        const respuesta = await api.post(`/categoria/${id}/restaurar`);
        return respuesta.data;
    },
};
