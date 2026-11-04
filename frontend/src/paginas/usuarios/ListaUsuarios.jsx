import { useEffect, useState, useContext } from 'react'; // 🚀 Importamos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos el contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaUsuarios from './TablaUsuarios';
import FormularioUsuario from './FormularioUsuario';
import { servicioUsuario } from '../../servicios/servicioUsuario';
import Swal from 'sweetalert2';

export default function ListaUsuarios() {
    // 🚀 Extraemos la función de permisos del contexto
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [usuarios, setUsuarios] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [usuarioAEditar, setUsuarioAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarUsuarios = async () => {
        try {
            const datos = await servicioUsuario.listar(incluirBajas);
            setUsuarios(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los usuarios.', 'error');
        }
    };

    useEffect(() => {
        cargarUsuarios();
    }, [incluirBajas]);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'El usuario será dado de baja en el sistema.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', // rose-600
            cancelButtonColor: '#64748b', // slate-500
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                await servicioUsuario.eliminar(id);
                Swal.fire('Eliminado', 'El usuario ha sido dado de baja.', 'success');
                cargarUsuarios();
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
            await servicioUsuario.restaurar(id);
            Swal.fire('Restaurado', 'El usuario vuelve a estar disponible.', 'success');
            cargarUsuarios();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar el usuario.', 'error');
        }
    };

    const handleAlternarEstado = async (usuario) => {
        try {
            if (usuario.activo) {
                await servicioUsuario.desactivar(usuario.id_usuario);
                Swal.fire('Desactivado', 'El usuario ha sido inactivado.', 'info');
            } else {
                await servicioUsuario.activar(usuario.id_usuario);
                Swal.fire('Activado', 'El usuario ahora está activo.', 'success');
            }
            cargarUsuarios();
        } catch (error) {
            Swal.fire('Error', 'No se pudo cambiar el estado del usuario.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setUsuarioAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (usuario) => {
        setUsuarioAEditar(usuario);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Control de Usuarios</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Gestión de accesos y credenciales del sistema
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para crear usuarios */}
                {tienePermiso('usuario_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nuevo Usuario
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
                    Mostrar usuarios eliminados (Bajas)
                </label>
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA */}
            <TablaUsuarios
                usuarios={usuarios}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                onAlternarEstado={handleAlternarEstado}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioUsuario
                    usuario={usuarioAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarUsuarios}
                />
            )}
        </Plantilla>
    );
}