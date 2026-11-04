import React, { createContext, useContext, useState, useEffect } from 'react';
import { servicioConfiguracion } from '../servicios/servicioConfiguracion';

// 🚀 Agregado "export" aquí
export const ContextoConfiguracion = createContext();

export const ProveedorConfiguracion = ({ children }) => {
    const [config, setConfig] = useState(null);
    const [cargando, setCargando] = useState(true);

    // Aplica las variables CSS al :root del DOM
    const aplicarVariablesEstilo = (datos) => {
        if (!datos) return;
        const root = document.documentElement;

        if (datos.color_primario) root.style.setProperty('--color-primario', datos.color_primario);
        if (datos.color_secundario) root.style.setProperty('--color-secundario', datos.color_secundario);
        if (datos.color_fondo) root.style.setProperty('--color-fondo', datos.color_fondo);
        if (datos.color_superficie) root.style.setProperty('--color-superficie', datos.color_superficie);
        if (datos.color_texto) root.style.setProperty('--color-texto', datos.color_texto);
        if (datos.fuente) root.style.setProperty('--fuente-sistema', datos.fuente);

        // Actualizar el título de la pestaña del navegador
        if (datos.nombre_sistema) {
            document.title = `${datos.nombre_sistema} ${datos.nombre_empresa ? `- ${datos.nombre_empresa}` : ''}`;
        }
    };

    const cargarConfiguracion = async () => {
        try {
            setCargando(true);
            const datos = await servicioConfiguracion.obtener();
            setConfig(datos);
            aplicarVariablesEstilo(datos);
        } catch (error) {
            console.error('Error al cargar la configuración del sistema:', error);
        } finally {
            setCargando(false);
        }
    };

    const actualizarConfiguracion = async (nuevosDatos) => {
        const respuesta = await servicioConfiguracion.actualizar(nuevosDatos);
        if (respuesta?.datos) {
            setConfig(respuesta.datos);
            aplicarVariablesEstilo(respuesta.datos);
        }
        return respuesta;
    };

    useEffect(() => {
        cargarConfiguracion();
    }, []);

    return (
        <ContextoConfiguracion.Provider
            value={{
                config,
                cargando,
                cargarConfiguracion,
                actualizarConfiguracion,
            }}
        >
            {children}
        </ContextoConfiguracion.Provider>
    );
};

// 🚀 Corregido a ContextoConfiguracion
export const useConfig = () => {
    const context = useContext(ContextoConfiguracion);
    if (!context) {
        throw new Error('useConfig debe usarse dentro de un ProveedorConfiguracion');
    }
    return context;
};