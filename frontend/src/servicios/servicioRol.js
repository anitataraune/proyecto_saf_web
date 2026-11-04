import api from '../api/axios';

export const servicioRol = {
    listar: async () => {
        const respuesta = await api.get('/rol');
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/rol', datos);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/rol/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/rol/${id}`);
        return respuesta.data;
    },
    listarPermisos: async () => {
        const respuesta = await api.get('/rol/sistema/permisos');
        return respuesta.data;
    },
};