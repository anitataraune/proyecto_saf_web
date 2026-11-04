import { useEffect, useState, useContext } from 'react';
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion';
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaActivos from './TablaActivos';
import { servicioActivo } from '../../servicios/servicioActivo';
import FormularioActivo from './FormularioActivo';
import Swal from 'sweetalert2';

export default function ListaActivos() {
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [activos, setActivos] = useState([]);
    const [modalAbierto, setModalAbierto] = useState(false);
    const [activoSeleccionado, setActivoSeleccionado] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false); // 🚀 Nuevo estado

    const cargarActivos = async () => {
        try {
            // 🚀 Pasamos el parámetro para incluir eliminados
            const datos = await servicioActivo.listar(incluirBajas);
            setActivos(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los activos fijos.', 'error');
        }
    };

    useEffect(() => {
        cargarActivos();
    }, [incluirBajas]); // 🚀 Se recarga cuando cambia el filtro

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Enviar a la papelera?',
            text: 'El activo se desactivará pero podrá ser restaurado posteriormente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioActivo.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'Activo enviado a la papelera.', 'success');
                cargarActivos();
            } catch (error) {
                Swal.fire(
                    'Error',
                    error?.response?.data?.mensaje || 'No se pudo eliminar el activo.',
                    'error'
                );
            }
        }
    };

    const handleRestaurar = async (id) => {
        try {
            const res = await servicioActivo.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Activo restaurado con éxito.', 'success');
            cargarActivos();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar el activo.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setActivoSeleccionado(null);
        setModalAbierto(true);
    };

    const handleAbrirEditar = (activo) => {
        setActivoSeleccionado({
            ...activo,
            categoria_id: activo.categoria_id ? String(activo.categoria_id) : '',
            ubicacion_id: activo.ubicacion_id ? String(activo.ubicacion_id) : '',
            proveedor_id: activo.proveedor_id ? String(activo.proveedor_id) : '',
        });
        setModalAbierto(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Control de Activos Fijos</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Gestión, asignación de categorías, control de estados y depreciación de bienes
                    </p>
                </div>

                {tienePermiso('activo_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nuevo Activo
                    </button>
                )}
            </div>

            {/* 🚀 Checkbox para mostrar eliminados (igual que en categorías) */}
            <div className="flex items-center mb-4 bg-white px-4 py-3 rounded-xl border border-slate-200">
                <label className="inline-flex items-center cursor-pointer select-none text-slate-700 font-medium text-sm">
                    <input
                        type="checkbox"
                        checked={incluirBajas}
                        onChange={(e) => setIncluirBajas(e.target.checked)}
                        className="w-4 h-4 rounded border-slate-300 mr-2"
                    />
                    Mostrar activos eliminados (Papelera)
                </label>
            </div>

            <TablaActivos
                activos={activos}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar} // 🚀 Pasamos la función restaurar
                tienePermiso={tienePermiso}
            />

            {modalAbierto && (
                <FormularioActivo
                    activo={activoSeleccionado}
                    cerrar={() => setModalAbierto(false)}
                    refrescar={cargarActivos}
                />
            )}
        </Plantilla>
    );
}