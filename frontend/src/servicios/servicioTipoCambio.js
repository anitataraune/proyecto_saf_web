import api from '../api/axios';

export const servicioTipoCambio = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/tipo-cambio', {
            params: { incluir_bajas: incluirBajas },
        });
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/tipo-cambio', datos);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/tipo-cambio/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/tipo-cambio/${id}`);
        return respuesta.data;
    },
    restaurar: async (id) => {
        const respuesta = await api.post(`/tipo-cambio/${id}/restaurar`);
        return respuesta.data;
    },
};
