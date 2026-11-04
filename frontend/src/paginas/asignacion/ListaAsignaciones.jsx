import { useEffect, useState, useContext } from 'react';
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion';
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaAsignaciones from './TablaAsignaciones';
import FormularioAsignacion from './FormularioAsignacion';
import { servicioAsignacion } from '../../servicios/servicioAsignacion';
import Swal from 'sweetalert2';

export default function ListaAsignaciones() {
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [asignaciones, setAsignaciones] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [asignacionAEditar, setAsignacionAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarAsignaciones = async () => {
        try {
            const datos = await servicioAsignacion.listar(incluirBajas);
            setAsignaciones(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar las asignaciones.', 'error');
        }
    };

    useEffect(() => {
        cargarAsignaciones();
    }, [incluirBajas]);

    const handleDevolver = async (asignacion) => {
        const { value: fecha } = await Swal.fire({
            title: 'Registrar Devolución',
            input: 'date',
            inputLabel: 'Fecha de recepción del activo',
            inputValue: new Date().toISOString().split('T')[0],
            showCancelButton: true,
            confirmButtonText: 'Confirmar Devolución',
            cancelButtonText: 'Cancelar',
        });

        if (fecha) {
            try {
                const res = await servicioAsignacion.registrarDevolucion(asignacion.id_asignacion, fecha);
                Swal.fire('Devuelto', res.mensaje || 'Devolución confirmada.', 'success');
                cargarAsignaciones();
            } catch (error) {
                Swal.fire('Error', error?.response?.data?.mensaje || 'No se pudo registrar la devolución.', 'error');
            }
        }
    };

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'La asignación será enviada a la papelera.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioAsignacion.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'En papelera.', 'success');
                cargarAsignaciones();
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar.', 'error');
            }
        }
    };

    const handleRestaurar = async (id) => {
        try {
            const res = await servicioAsignacion.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Asignación activa.', 'success');
            cargarAsignaciones();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar.', 'error');
        }
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Asignación de Activos</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Control de activos fijos entregados al personal de la empresa
                    </p>
                </div>

                {tienePermiso('asignacion_guardar') && (
                    <button
                        onClick={() => { setAsignacionAEditar(null); setMostrarModal(true); }}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nueva Asignación
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
                    Mostrar eliminados (Papelera)
                </label>
            </div>

            <TablaAsignaciones
                asignaciones={asignaciones}
                onEditar={(asig) => { setAsignacionAEditar(asig); setMostrarModal(true); }}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                onDevolver={handleDevolver}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioAsignacion
                    asignacion={asignacionAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarAsignaciones}
                />
            )}
        </Plantilla>
    );
}