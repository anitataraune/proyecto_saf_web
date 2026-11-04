import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import { servicioUsuarioRol } from '../../servicios/servicioUsuarioRol';
import { obtenerReglasUsuarioRol } from './validacionesUsuarioRol';
import Swal from 'sweetalert2';

export default function FormularioUsuarioRol({ asignacion, cerrar, refrescar }) {
    const isEditMode = !!asignacion;
    const [usuarios, setUsuarios] = useState([]);
    const [rolesDisponibles, setRolesDisponibles] = useState([]);
    const [cargandoCatalogos, setCargandoCatalogos] = useState(true);

    const reglas = obtenerReglasUsuarioRol(isEditMode);

    const {
        register,
        handleSubmit,
        setValue,
        setError, // Para inyectar errores provenientes de Laravel (422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario interactúa
        defaultValues: {
            usuario_id: '',
            roles: [],
        },
    });

    // Cargar catálogos iniciales
    useEffect(() => {
        const cargarListas = async () => {
            try {
                const [listaUsuarios, listaRoles] = await Promise.all([
                    servicioUsuarioRol.listarUsuarios(),
                    servicioUsuarioRol.listarRoles()
                ]);
                setUsuarios(listaUsuarios);
                setRolesDisponibles(listaRoles);
            } catch (error) {
                Swal.fire('Error', 'No se pudieron estructurar los selectores del formulario.', 'error');
            } finally {
                setCargandoCatalogos(false);
            }
        };
        cargarListas();
    }, []);

    // Preparar el formulario si viene una asignación para editar
    useEffect(() => {
        if (isEditMode && rolesDisponibles.length > 0) {
            setValue('usuario_id', asignacion.usuario_id);
            const rolesNombres = asignacion.roles ? asignacion.roles.map(r => r.name) : [];
            setValue('roles', rolesNombres);
        }
    }, [asignacion, isEditMode, setValue, rolesDisponibles]);

    const onSubmit = async (data) => {
        try {
            if (isEditMode) {
                const res = await servicioUsuarioRol.actualizar(asignacion.usuario_id, { roles: data.roles });
                Swal.fire('Actualizado', res.message || 'Roles modificados.', 'success');
            } else {
                const res = await servicioUsuarioRol.guardar(data);
                Swal.fire('Asignado', res.message || 'Asignación registrada.', 'success');
            }
            refrescar();
            cerrar();
        } catch (error) {
            // Manejo de errores de validación del backend (Código HTTP 422 de Laravel)
            if (error?.response?.status === 422 && error?.response?.data?.errors) {
                const erroresBackend = error.response.data.errors;

                Object.keys(erroresBackend).forEach((campo) => {
                    setError(campo, {
                        type: 'server',
                        message: erroresBackend[campo][0],
                    });
                });
            } else {
                Swal.fire(
                    'Error',
                    error?.response?.data?.mensaje || error?.response?.data?.message || 'No se pudo procesar la asignación de roles.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal
            titulo={isEditMode ? '⚙️ Modificar Asignación de Roles' : '👥 Nueva Asignación de Roles'}
            cerrar={cerrar}
        >
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
                
                {/* Selector de Usuario */}
                <div>
                    <label className="block text-sm font-semibold text-slate-700 mb-1.5">
                        Seleccionar Usuario
                    </label>
                    <select
                        {...register('usuario_id', reglas.usuario_id)}
                        disabled={isEditMode || cargandoCatalogos}
                        className={`w-full p-2.5 bg-white border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition ${
                            errors.usuario_id ? 'border-rose-500' : 'border-slate-300'
                        } ${isEditMode ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : ''}`}
                    >
                        <option value="">-- Elige un usuario --</option>
                        {usuarios.map((u) => (
                            <option key={u.id_usuario} value={u.id_usuario}>
                                {u.usuario} (ID: #{u.id_usuario})
                            </option>
                        ))}
                    </select>
                    {errors.usuario_id && (
                        <p className="text-xs text-rose-500 mt-1">{errors.usuario_id.message}</p>
                    )}
                </div>

                {/* Listado de Checkboxes de Roles */}
                <div>
                    <label className="block text-sm font-semibold text-slate-700 mb-2">
                        Perfiles / Roles a otorgar
                    </label>
                    
                    {cargandoCatalogos ? (
                        <p className="text-sm text-slate-400">Cargando roles disponibles...</p>
                    ) : (
                        <div className="grid grid-cols-1 gap-2.5 max-h-52 overflow-y-auto p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            {rolesDisponibles.map((rol) => (
                                <label 
                                    key={rol.id} 
                                    className="flex items-center p-2.5 bg-white rounded-lg border border-slate-100 hover:border-slate-300 shadow-sm cursor-pointer select-none transition text-sm text-slate-700"
                                >
                                    <input
                                        type="checkbox"
                                        value={rol.name}
                                        {...register('roles', reglas.roles)}
                                        className="w-4 h-4 text-purple-600 bg-slate-100 border-slate-300 rounded focus:ring-purple-500 mr-3"
                                    />
                                    <div className="flex flex-col">
                                        <span className="font-bold text-slate-800 uppercase text-xs tracking-wider bg-slate-100 px-2 py-0.5 rounded border border-slate-200 w-max mb-0.5">
                                            {rol.name}
                                        </span>
                                    </div>
                                </label>
                            ))}
                        </div>
                    )}
                    {errors.roles && (
                        <p className="text-xs text-rose-500 mt-1">{errors.roles.message}</p>
                    )}
                </div>

                {/* Footer del Modal */}
                <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        onClick={cerrar}
                        disabled={isSubmitting}
                        className="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-lg ring-1 ring-inset ring-slate-300 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        disabled={isSubmitting || cargandoCatalogos}
                        className="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg shadow-sm transition"
                    >
                        {isSubmitting ? 'Guardando...' : 'Guardar Cambios'}
                    </button>
                </div>
            </form>
        </Modal>
    );
}