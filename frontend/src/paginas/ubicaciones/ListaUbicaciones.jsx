import { useEffect, useState, useContext } from 'react'; // 🚀 Importamos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos el contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaUbicaciones from './TablaUbicaciones';
import FormularioUbicacion from './FormularioUbicacion';
import { servicioUbicacion } from '../../servicios/servicioUbicacion';
import Swal from 'sweetalert2';

export default function ListaUbicaciones() {
    // 🚀 Extraemos la función de permisos del contexto
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [ubicaciones, setUbicaciones] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [ubicacionAEditar, setUbicacionAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarUbicaciones = async () => {
        try {
            const datos = await servicioUbicacion.listar(incluirBajas);
            setUbicaciones(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar las ubicaciones.', 'error');
        }
    };

    useEffect(() => {
        cargarUbicaciones();
    }, [incluirBajas]);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'La ubicación será enviada a la papelera.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioUbicacion.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'Ubicación en papelera.', 'success');
                cargarUbicaciones();
            } catch (error) {
                Swal.fire(
                    'Error',
                    error?.response?.data?.mensaje || 'No se pudo eliminar.',
                    'error'
                );
            }
        }
    };

    const handleRestaurar = async (id) => {
        try {
            const res = await servicioUbicacion.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Ubicación activa de nuevo.', 'success');
            cargarUbicaciones();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar la ubicación.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setUbicacionAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (ubicacion) => {
        setUbicacionAEditar(ubicacion);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Ubicaciones Físicas</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Estructura de distribución de los Activos Fijos
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para crear ubicaciones */}
                {tienePermiso('ubicacion_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nueva Ubicación
                    </button>
                )}
            </div>

            <div className="flex items-center mb-4 bg-white px-4 py-3 rounded-xl border border-slate-200">
                <label className="inline-flex items-center cursor-pointer select-none text-slate-700 font-medium text-sm">
                    <input
                        type="checkbox"
                        checked={incluirBajas}
                        onChange={(e) => setIncluirBajas(e.target.checked)}
                        className="w-4 h-4 rounded border-slate-300 mr-2"
                    />
                    Mostrar ubicaciones eliminadas (Papelera)
                </label>
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA */}
            <TablaUbicaciones
                ubicaciones={ubicaciones}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioUbicacion
                    ubicacion={ubicacionAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarUbicaciones}
                />
            )}
        </Plantilla>
    );
}