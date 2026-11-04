import { useEffect, useState, useContext } from 'react'; // 🚀 Importamos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos el contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaRoles from './TablaRoles';
import FormularioRol from './FormularioRol';
import { servicioRol } from '../../servicios/servicioRol';
import Swal from 'sweetalert2';

export default function ListaRoles() {
    // 🚀 Extraemos la función de permisos
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [roles, setRoles] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [rolAEditar, setRolAEditar] = useState(null);

    const cargarRoles = async () => {
        try {
            const datos = await servicioRol.listar();
            setRoles(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los roles del sistema.', 'error');
        }
    };

    useEffect(() => {
        cargarRoles();
    }, []);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el rol y desvinculará sus accesos.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioRol.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'Rol borrado con éxito.', 'success');
                cargarRoles();
            } catch (error) {
                Swal.fire(
                    'Error',
                    error?.response?.data?.mensaje || 'No se pudo eliminar el rol.',
                    'error'
                );
            }
        }
    };

    const handleAbrirCrear = () => {
        setRolAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (rol) => {
        setRolAEditar(rol);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Roles y Permisos</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Configuración de perfiles de seguridad y asignación de accesos al sistema
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para crear roles */}
                {tienePermiso('rol_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nuevo Rol
                    </button>
                )}
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA */}
            <TablaRoles
                roles={roles}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioRol
                    rol={rolAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarRoles}
                />
            )}
        </Plantilla>
    );
}