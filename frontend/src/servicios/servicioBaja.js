import api from '../api/axios';

export const servicioBaja = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/baja', { params: { incluir_bajas: incluirBajas } });
        return respuesta.data;
    },
    // Trae únicamente activos habilitados para dar de baja
    obtenerActivosDisponibles: async () => {
        const respuesta = await api.get('/activo/disponibles');
        return respuesta.data;
    },
    guardar: async (datos) => {
        const respuesta = await api.post('/baja', datos);
        return respuesta.data;
    },
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/baja/${id}`, datos);
        return respuesta.data;
    },
    eliminar: async (id) => {
        const respuesta = await api.delete(`/baja/${id}`);
        return respuesta.data;
    },
    restaurar: async (id) => {
        const respuesta = await api.post(`/baja/${id}/restaurar`);
        return respuesta.data;
    },
};