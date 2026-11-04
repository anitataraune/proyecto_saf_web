import api from "../api/axios";

// Función auxiliar privada para interpretar errores JSON dentro de respuestas tipo Blob
const procesarErrorBlob = async (error) => {
    // Si la respuesta fue un Blob (error 400, 401, 403, 500, etc.)
    if (error.response && error.response.data instanceof Blob) {
        try {
            const textoError = await error.response.data.text();
            const jsonError = JSON.parse(textoError);
            throw new Error(jsonError.message || jsonError.error || 'Error al generar el reporte PDF.');
        } catch (e) {
            if (e.message && e.message !== 'Error al generar el reporte PDF.') {
                throw e; // Re-lanzar el mensaje parseado
            }
            throw new Error('Error al procesar el archivo emitido por el servidor.');
        }
    }
    throw error;
};

// Función auxiliar privada para forzar la descarga en el navegador
const descargarArchivoBlob = (data, nombreArchivo) => {
    const url = window.URL.createObjectURL(new Blob([data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', nombreArchivo);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
};

export const servicioReporte = {
    // 1. Reporte General
    reporteGeneral: async () => {
        try {
            const respuesta = await api.get('/reporte/activo-general', {
                responseType: 'blob',
            });
            descargarArchivoBlob(respuesta.data, 'Reporte_General_Activos.pdf');
        } catch (error) {
            await procesarErrorBlob(error);
        }
    },

    // 2. Reporte por Ubicación
    reportePorUbicacion: async (ubicacionId = null) => {
        try {
            const params = ubicacionId ? { ubicacion_id: ubicacionId } : {};
            const respuesta = await api.get('/reporte/activo-ubicacion', {
                params,
                responseType: 'blob',
            });
            descargarArchivoBlob(respuesta.data, 'Reporte_Activos_Ubicacion.pdf');
        } catch (error) {
            await procesarErrorBlob(error);
        }
    },

    // 3. Reporte por Categoría
    reportePorCategoria: async (categoriaId = null) => {
        try {
            const params = categoriaId ? { categoria_id: categoriaId } : {};
            const respuesta = await api.get('/reporte/activo-categoria', {
                params,
                responseType: 'blob',
            });
            descargarArchivoBlob(respuesta.data, 'Reporte_Activos_Categoria.pdf');
        } catch (error) {
            await procesarErrorBlob(error);
        }
    },
};