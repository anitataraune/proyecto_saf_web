import { useState, useEffect } from 'react';
import Plantilla from '../../componentes/estructura/Plantilla';
import { servicioReporte } from '../../servicios/servicioReporte';
import api from '../../api/axios';
import Swal from 'sweetalert2';

export default function ReporteActivoCategoria() {
    const [categorias, setCategorias] = useState([]);
    const [categoriaSeleccionada, setCategoriaSeleccionada] = useState('');
    const [activos, setActivos] = useState([]);
    const [cargandoTabla, setCargandoTabla] = useState(true);
    const [descargando, setDescargando] = useState(false);

    useEffect(() => {
        // Cargar categorías y activos en paralelo
        Promise.all([
            api.get('/categoria'),
            api.get('/activo')
        ])
            .then(([resCat, resAct]) => {
                setCategorias(Array.isArray(resCat.data) ? resCat.data : resCat.data.data || []);
                setActivos(Array.isArray(resAct.data) ? resAct.data : resAct.data.data || []);
            })
            .catch((err) => console.error('Error al cargar datos:', err))
            .finally(() => setCargandoTabla(false));
    }, []);

    // Filtrar activos según la categoría seleccionada
    const activosFiltrados = categoriaSeleccionada
        ? activos.filter(a => String(a.categoria_id) === String(categoriaSeleccionada))
        : activos;

    const valorTotal = activosFiltrados.reduce((acc, item) => acc + parseFloat(item.valor_compra || 0), 0);

    const descargarPDF = async () => {
        setDescargando(true);
        try {
            await servicioReporte.reportePorCategoria(categoriaSeleccionada);
        } catch (error) {
            Swal.fire('Error', error.message || 'No se pudo generar el reporte.', 'error');
        } finally {
            setDescargando(false);
        }
    };

    return (
        <Plantilla>
            <div className="max-w-7xl mx-auto space-y-6">
                {/* Cabecera y Botón */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 className="text-2xl font-bold" style={{ color: 'var(--color-texto)' }}>
                            Reporte de Activos Fijos por Categoría
                        </h2>
                        <p className="text-slate-500 text-sm mt-1">
                            Filtra por categoría específica y descarga el documento oficial en PDF.
                        </p>
                    </div>
                    <button
                        onClick={descargarPDF}
                        disabled={descargando}
                        className="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg shadow-md transition flex items-center gap-2"
                    >
                        {descargando ? 'Generando PDF...' : '📄 Descargar Reporte'}
                    </button>
                </div>

                {/* Filtro y Tarjetas de Resumen */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="tarjeta p-4 flex flex-col justify-center">
                        <label className="block text-sm font-medium mb-1 text-slate-700">Filtrar por Categoría:</label>
                        <select
                            value={categoriaSeleccionada}
                            onChange={(e) => setCategoriaSeleccionada(e.target.value)}
                            className="w-full border rounded-lg p-2 text-sm bg-white"
                        >
                            <option value="">Todas las Categorías</option>
                            {categorias.map((c) => (
                                <option key={c.id_categoria} value={c.id_categoria}>{c.nombre}</option>
                            ))}
                        </select>
                    </div>
                    <div className="tarjeta p-4 flex items-center justify-between">
                        <div>
                            <p className="text-sm text-slate-500 font-medium">Activos en este filtro</p>
                            <h3 className="text-2xl font-bold mt-1">{activosFiltrados.length}</h3>
                        </div>
                        <div className="p-3 bg-blue-50 text-blue-600 rounded-xl text-xl"></div>
                    </div>
                    <div className="tarjeta p-4 flex items-center justify-between">
                        <div>
                            <p className="text-sm text-slate-500 font-medium">Valor Total</p>
                            <h3 className="text-2xl font-bold mt-1">Bs.{valorTotal.toLocaleString('es-ES', { minimumFractionDigits: 2 })}</h3>
                        </div>
                        <div className="p-3 bg-green-50 text-green-600 rounded-xl text-xl"></div>
                    </div>
                </div>

                {/* Tabla de Vista Previa */}
                <div className="tarjeta p-6 space-y-4">
                    <h3 className="text-lg font-semibold text-slate-700">Vista Previa de Datos a Exportar</h3>
                    
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr className="border-b bg-slate-50 text-slate-600">
                                    <th className="p-3 font-medium">ID</th>
                                    <th className="p-3 font-medium">Descripción</th>
                                    <th className="p-3 font-medium">Valor Compra</th>
                                    <th className="p-3 font-medium">Fecha Compra</th>
                                    <th className="p-3 font-medium">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                {cargandoTabla ? (
                                    <tr>
                                        <td colSpan="5" className="p-6 text-center text-slate-400">Cargando vista previa...</td>
                                    </tr>
                                ) : activosFiltrados.length === 0 ? (
                                    <tr>
                                        <td colSpan="5" className="p-6 text-center text-slate-400">No hay activos para esta categoría.</td>
                                    </tr>
                                ) : (
                                    activosFiltrados.slice(0, 5).map((activo) => (
                                        <tr key={activo.id_activo} className="border-b hover:bg-slate-50/50 transition">
                                            <td className="p-3 font-mono text-slate-600">#{activo.id_activo}</td>
                                            <td className="p-3 font-medium text-slate-800">{activo.descripcion}</td>
                                            <td className="p-3">Bs.{parseFloat(activo.valor_compra || 0).toFixed(2)}</td>
                                            <td className="p-3 text-slate-600">{activo.fecha_compra}</td>
                                            <td className="p-3">
                                                <span className="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">
                                                    {activo.estado_actual || 'Activo'}
                                                </span>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                    {activosFiltrados.length > 5 && (
                        <p className="text-xs text-slate-400 text-right">Mostrando los primeros 5 de {activosFiltrados.length} registros filtrados.</p>
                    )}
                </div>
            </div>
        </Plantilla>
    );
}