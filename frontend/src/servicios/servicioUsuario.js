import api from '../api/axios';

export const servicioUsuario = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/usuario', { params: { incluir_bajas: incluirBajas } });
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/usuario', datos);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/usuario/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/usuario/${id}`);
        return respuesta.data;
    },
    restaurar: async (id) => {
        const respuesta = await api.post(`/usuario/${id}/restaurar`);
        return respuesta.data;
    },
    activar: async (id) => {
        const respuesta = await api.patch(`/usuario/${id}/activar`);
        return respuesta.data;
    },
    desactivar: async (id) => {
        const respuesta = await api.patch(`/usuario/${id}/desactivar`);
        return respuesta.data;
    },
};
