import { useEffect, useState, useContext } from 'react'; // 🚀 Importamos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos el contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaUsuarioRol from './TablaUsuarioRol';
import FormularioUsuarioRol from './FormularioUsuarioRol';
import { servicioUsuarioRol } from '../../servicios/servicioUsuarioRol';
import Swal from 'sweetalert2';

export default function ListaUsuarioRol() {
    // 🚀 Extraemos la función de permisos del contexto
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [asignaciones, setAsignaciones] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [asignacionAEditar, setAsignacionAEditar] = useState(null);

    const cargarAsignaciones = async () => {
        try {
            const datos = await servicioUsuarioRol.listar();
            setAsignaciones(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar las asignaciones de roles.', 'error');
        }
    };

    useEffect(() => {
        cargarAsignaciones();
    }, []);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Remover todos los roles?',
            text: 'Esta acción desvinculará al usuario de todos sus perfiles asignados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, remover',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioUsuarioRol.eliminar(id);
                Swal.fire('Removido', res.message || 'Roles revocados con éxito.', 'success');
                cargarAsignaciones();
            } catch (error) {
                Swal.fire(
                    'Error',
                    error?.response?.data?.mensaje || 'No se pudieron remover los roles.',
                    'error'
                );
            }
        }
    };

    const handleAbrirCrear = () => {
        setAsignacionAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (asignacion) => {
        setAsignacionAEditar(asignacion);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Asignación de Roles</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Mantenimiento independiente para vincular usuarios con sus respectivos roles del sistema
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para asignar roles */}
                {tienePermiso('usuario_rol_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Asignar Rol
                    </button>
                )}
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA */}
            <TablaUsuarioRol
                asignaciones={asignaciones}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioUsuarioRol
                    asignacion={asignacionAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarAsignaciones}
                />
            )}
        </Plantilla>
    );
}