import { createContext, useState } from 'react';

export const ContextoAutenticacion = createContext();

export function ProveedorAutenticacion({ children }) {
    // 1. Recuperar el objeto de usuario de forma segura con try...catch
    const [usuario, setUsuario] = useState(() => {
        const usuarioGuardado = localStorage.getItem('usuario');
        if (!usuarioGuardado) return null;

        try {
            return JSON.parse(usuarioGuardado);
        } catch (error) {
            console.warn("⚠️ El valor guardado en 'usuario' no era un JSON válido. Limpiando localStorage...", error);
            localStorage.removeItem('usuario');
            return null;
        }
    });

    // 2. Comprobar autenticación: Debe existir TANTO el token COMO los datos del usuario válidos
    const [autenticado, setAutenticado] = useState(() => {
        const token = localStorage.getItem('token');
        const usuarioGuardado = localStorage.getItem('usuario');
        return !!(token && usuarioGuardado);
    });

    // 3. Método para iniciar sesión
    const iniciar = (token, datosUsuario) => {
        localStorage.setItem('token', token);
        // Guardamos la estructura serializada para mantener tipos y permisos
        localStorage.setItem('usuario', JSON.stringify(datosUsuario));

        setUsuario(datosUsuario);
        setAutenticado(true);
    };

    // 4. Método para cerrar sesión
    const cerrar = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('usuario');
        setUsuario(null);
        setAutenticado(false);
    };

    // 5. Helper de permisos con soporte para superusuario
    const tienePermiso = (permisoRequerido) => {
        if (!usuario) return false;

        // Si es administrador general, tiene acceso total
        if (usuario.es_admin === true) {
            return true;
        }

        // Verificar en el listado de permisos del rol
        return Array.isArray(usuario.permisos) ? usuario.permisos.includes(permisoRequerido) : false;
    };

    return (
        <ContextoAutenticacion.Provider 
            value={{ 
                autenticado, 
                usuario, 
                iniciar, 
                cerrar, 
                tienePermiso 
            }}
        >
            {children}
        </ContextoAutenticacion.Provider>
    );
}