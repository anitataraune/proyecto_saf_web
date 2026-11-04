import api from '../api/axios';

export const servicioAsignacion = {
    listar: async (incluirBajas = false) => {
        const respuesta = await api.get('/asignacion', { params: { incluir_bajas: incluirBajas } });
        return respuesta.data;
    },
    
    mostrar: async (id) => {
        try {
            const respuesta = await api.get(`/asignacion/${id}`);
            return respuesta.data;
        } catch (error) {
            console.error('Error al obtener asignación:', error);
            throw error;
        }
    },
    
    guardar: async (datos) => {
        const respuesta = await api.post('/asignacion', datos);
        return respuesta.data;
    },
    
    registrarDevolucion: async (id, fechaDevolucion) => {
        const respuesta = await api.post(`/asignacion/${id}/devolucion`, { fecha_devolucion: fechaDevolucion });
        return respuesta.data;
    },
    
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/asignacion/${id}`, datos);
        return respuesta.data;
    },
    
    eliminar: async (id) => {
        const respuesta = await api.delete(`/asignacion/${id}`);
        return respuesta.data;
    },
    
    restaurar: async (id) => {
        const respuesta = await api.post(`/asignacion/${id}/restaurar`);
        return respuesta.data;
    },
};