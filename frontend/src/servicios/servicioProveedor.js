import api from '../api/axios';

export const servicioProveedor = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/proveedor', { params: { incluir_bajas: incluirBajas } });
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/proveedor', datos);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/proveedor/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/proveedor/${id}`);
        return respuesta.data;
    },
    restaurar: async (id) => {
        const respuesta = await api.post(`/proveedor/${id}/restaurar`);
        return respuesta.data;
    },
};
