import api from '../api/axios';

export const servicioPolizaSeguro = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/poliza-seguro', {
            params: { incluir_bajas: incluirBajas },
        });
        return respuesta.data;
    },

    listarActivosAgrupados: async () => {
        const respuesta = await api.get('/poliza-seguro/activos-agrupados');
        return respuesta.data;
    },

    guardar: async (datos) => {
        const respuesta = await api.post('/poliza-seguro', datos);
        return respuesta.data;
    },

    sincronizarActivos: async (id, datos) => {
        const respuesta = await api.post(`/poliza-seguro/${id}/activos`, datos);
        return respuesta.data;
    },

    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/poliza-seguro/${id}`, datos);
        return respuesta.data;
    },

    eliminar: async (id) => {
        const respuesta = await api.delete(`/poliza-seguro/${id}`);
        return respuesta.data;
    },

    restaurar: async (id) => {
        const respuesta = await api.post(`/poliza-seguro/${id}/restaurar`);
        return respuesta.data;
    },
};