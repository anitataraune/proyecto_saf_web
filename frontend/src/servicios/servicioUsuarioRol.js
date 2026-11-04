import api from '../api/axios';

export const servicioUsuarioRol = {
    listar: async () => {
        const respuesta = await api.get('/usuario-rol');
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/usuario-rol', datos);
        return respuesta.data;
    },
    mostrar: async (id) => {
        const respuesta = await api.get(`/usuario-rol/${id}`);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/usuario-rol/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/usuario-rol/${id}`);
        return respuesta.data;
    },
    // Métodos auxiliares para alimentar selectores y checkboxes
    listarUsuarios: async () => {
        const respuesta = await api.get('/usuario');
        return respuesta.data;
    },
    listarRoles: async () => {
        const respuesta = await api.get('/rol');
        return respuesta.data;
    }
};