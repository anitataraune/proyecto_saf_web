import api from '../api/axios';

export const servicioResponsable = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/responsable', {
            params: { incluir_bajas: incluirBajas },
        });
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/responsable', datos);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/responsable/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/responsable/${id}`);
        return respuesta.data;
    },
    restaurar: async (id) => {
        const respuesta = await api.post(`/responsable/${id}/restaurar`);
        return respuesta.data;
    },
};
