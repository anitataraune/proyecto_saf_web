import { useEffect, useState, useContext } from 'react'; // 🚀 Importamos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos el contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaResponsables from './TablaResponsables';
import FormularioResponsable from './FormularioResponsable';
import { servicioResponsable } from '../../servicios/servicioResponsable';
import Swal from 'sweetalert2';

export default function ListaResponsables() {
    // 🚀 Extraemos la función de permisos
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [responsables, setResponsables] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [responsableAEditar, setResponsableAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarResponsables = async () => {
        try {
            const datos = await servicioResponsable.listar(incluirBajas);
            setResponsables(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los responsables.', 'error');
        }
    };

    useEffect(() => {
        cargarResponsables();
    }, [incluirBajas]);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'El responsable será enviado a la papelera.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioResponsable.eliminar(id);
                Swal.fire(
                    'Eliminado',
                    res.mensaje || 'Responsable enviado a la papelera.',
                    'success'
                );
                cargarResponsables();
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
            const res = await servicioResponsable.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Responsable activo de nuevo.', 'success');
            cargarResponsables();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar al responsable.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setResponsableAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (responsable) => {
        setResponsableAEditar(responsable);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Responsables de Activos</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Personal encargado de la custodia y asignación de bienes
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para crear responsables */}
                {tienePermiso('responsable_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nuevo Responsable
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
                    Mostrar responsables eliminados (Papelera)
                </label>
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA */}
            <TablaResponsables
                responsables={responsables}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioResponsable
                    responsable={responsableAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarResponsables}
                />
            )}
        </Plantilla>
    );
}