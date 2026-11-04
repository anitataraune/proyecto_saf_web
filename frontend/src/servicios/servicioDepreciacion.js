import api from '../api/axios';

export const servicioDepreciacion = {
    listar: async (activoId = null, incluirBajas = false) => {
        const params = { incluir_bajas: incluirBajas };
        if (activoId) params.activo_id = activoId;
        const respuesta = await api.get('/depreciacion', { params });
        return respuesta.data;
    },
    procesar: async (mes, anio) => {
        const respuesta = await api.post('/depreciacion/procesar', { mes, anio });
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/depreciacion/${id}`);
        return respuesta.data;
    },
    restaurar: async (id) => {
        const respuesta = await api.post(`/depreciacion/${id}/restaurar`);
        return respuesta.data;
    },
};