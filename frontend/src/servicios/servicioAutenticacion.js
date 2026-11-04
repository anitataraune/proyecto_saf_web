import api from '../api/axios';

export const servicioAutenticacion = {
    login: async (credenciales) => {
        const respuesta = await api.post('/autenticacion/iniciar-sesion', credenciales);
        return respuesta.data;
    },
    logout: async () => {
        // Corregida la ruta para coincidir con tu backend
        const respuesta = await api.post('/autenticacion/cerrar-sesion');
        return respuesta.data;
    },
};
