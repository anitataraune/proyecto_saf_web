import api from '../api/axios';

export const servicioActivo = {
    listar: async (incluirEliminados = false) => {
        try {
            const url = incluirEliminados ? '/activo?eliminados=true' : '/activo';
            const respuesta = await api.get(url);
            return respuesta.data;
        } catch (error) {
            console.error('Error al listar activos:', error);
            throw error;
        }
    },
    
    // Método para obtener activos disponibles (no asignados)
    listarDisponibles: async () => {
        try {
            // Obtenemos todos los activos
            const activos = await servicioActivo.listar();
            
            // Filtramos solo los que están activos y no tienen asignación actual
            // Nota: Esto asume que tu backend devuelve la relación 'asignacionActual'
            return activos.filter(activo => 
                activo.estado_actual === 'activo' && 
                (!activo.asignacion_actual || activo.asignacion_actual.estado_asignacion !== 'asignado')
            );
        } catch (error) {
            console.error('Error al listar activos disponibles:', error);
            throw error;
        }
    },
    
    guardar: async (datos) => {
        const respuesta = await api.post('/activo', datos);
        return respuesta.data;
    },
    
    mostrar: async (id) => {
        const respuesta = await api.get(`/activo/${id}`);
        return respuesta.data;
    },
    
    actualizar: async (id, datos) => {
        const respuesta = await api.put(`/activo/${id}`, datos);
        return respuesta.data;
    },
    
    eliminar: async (id) => {
        const respuesta = await api.delete(`/activo/${id}`);
        return respuesta.data;
    },
    
    restaurar: async (id) => {
        const respuesta = await api.post(`/activo/${id}/restaurar`);
        return respuesta.data;
    },
    
    listarCategorias: async () => {
        const respuesta = await api.get('/categoria');
        return respuesta.data;
    },

    listarProveedores: async () => {
        const respuesta = await api.get('/proveedor');
        return respuesta.data;
    }
};