import { useEffect, useState, useMemo } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioRol } from '../../servicios/servicioRol';
import { reglasRol } from './validacionesRol';
import Swal from 'sweetalert2';

export default function FormularioRol({ rol, cerrar, refrescar }) {
    const isEditMode = !!rol;
    const [catalogoPermisos, setCatalogoPermisos] = useState([]);
    const [cargandoPermisos, setCargandoPermisos] = useState(true);
    
    const [modulosAbiertos, setModulosAbiertos] = useState({});

    const {
        register,
        handleSubmit,
        setValue,
        watch,
        setError, // Para inyectar errores de Laravel (422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real
        defaultValues: {
            name: '',
            permisos: [],
        },
    });

    const permisosSeleccionados = watch('permisos') || [];

    // Cargar catálogo de permisos
    useEffect(() => {
        const cargarCatalogos = async () => {
            try {
                const lista = await servicioRol.listarPermisos();
                setCatalogoPermisos(lista);
            } catch (error) {
                Swal.fire('Error', 'No se pudo cargar la lista de permisos.', 'error');
            } finally {
                setCargandoPermisos(false);
            }
        };
        cargarCatalogos();
    }, []);

    // Cargar datos en modo edición
    useEffect(() => {
        if (isEditMode && catalogoPermisos.length > 0) {
            setValue('name', rol.name);
            const nombresPermisos = rol.permissions ? rol.permissions.map(p => p.name) : [];
            setValue('permisos', nombresPermisos);
        }
    }, [rol, isEditMode, setValue, catalogoPermisos]);

    const permisosAgrupados = useMemo(() => {
        const grupos = {};

        catalogoPermisos.forEach((permiso) => {
            const partes = permiso.name.split('_');
            let modulo = 'GENERAL';

            if (partes.length > 1) {
                partes.pop();
                modulo = partes.join(' ').toUpperCase();
            }

            if (!grupos[modulo]) {
                grupos[modulo] = [];
            }
            grupos[modulo].push(permiso);
        });

        return grupos;
    }, [catalogoPermisos]);

    const toggleModulo = (modulo) => {
        setModulosAbiertos(prev => ({
            ...prev,
            [modulo]: !prev[modulo]
        }));
    };

    const handleToggleGrupo = (permisosGrupo) => {
        const nombresGrupo = permisosGrupo.map(p => p.name);
        const todosSeleccionados = nombresGrupo.every(p => permisosSeleccionados.includes(p));

        let nuevosPermisos;
        if (todosSeleccionados) {
            nuevosPermisos = permisosSeleccionados.filter(p => !nombresGrupo.includes(p));
        } else {
            nuevosPermisos = Array.from(new Set([...permisosSeleccionados, ...nombresGrupo]));
        }
        setValue('permisos', nuevosPermisos, { shouldValidate: true });
    };

    const handleToggleTodo = () => {
        if (permisosSeleccionados.length === catalogoPermisos.length) {
            setValue('permisos', [], { shouldValidate: true });
        } else {
            setValue('permisos', catalogoPermisos.map(p => p.name), { shouldValidate: true });
        }
    };

    const onSubmit = async (data) => {
        try {
            if (isEditMode) {
                const res = await servicioRol.actualizar(rol.id, data);
                Swal.fire('Actualizado', res.mensaje || 'Rol modificado.', 'success');
            } else {
                const res = await servicioRol.guardar(data);
                Swal.fire('Guardado', res.mensaje || 'Rol registrado.', 'success');
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
                    error?.response?.data?.mensaje || error?.response?.data?.message || 'No se pudo procesar la información del rol.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal
            titulo={isEditMode ? '⚙️ Editar Rol y Permisos' : '🔑 Nuevo Rol de Sistema'}
            cerrar={cerrar}
            tamano="2xl"
        >
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
                <CampoTexto
                    label="Nombre del Rol"
                    name="name"
                    placeholder="Ej. administrador, operador_ventas"
                    register={register}
                    errors={errors}
                    validation={reglasRol.name}
                />

                <div>
                    <div className="flex justify-between items-center mb-2">
                        <label className="text-sm font-semibold text-slate-700">
                            Asignación de Permisos ({permisosSeleccionados.length} / {catalogoPermisos.length})
                        </label>
                        
                        {!cargandoPermisos && catalogoPermisos.length > 0 && (
                            <button
                                type="button"
                                onClick={handleToggleTodo}
                                className="text-xs text-blue-600 hover:text-blue-800 font-semibold"
                            >
                                {permisosSeleccionados.length === catalogoPermisos.length
                                    ? 'Desmarcar Todos'
                                    : 'Seleccionar Todos'}
                            </button>
                        )}
                    </div>
                    
                    {cargandoPermisos ? (
                        <p className="text-sm text-slate-400 py-4 text-center">Cargando catálogo de permisos...</p>
                    ) : (
                        <div className="max-h-80 overflow-y-auto space-y-2 pr-1">
                            {Object.keys(permisosAgrupados).map((modulo) => {
                                const permisosModulo = permisosAgrupados[modulo];
                                const seleccionadosEnModulo = permisosModulo.filter(p => 
                                    permisosSeleccionados.includes(p.name)
                                ).length;
                                const estaAbierto = modulosAbiertos[modulo] ?? true;

                                return (
                                    <div key={modulo} className="border border-slate-200 rounded-lg overflow-hidden bg-white shadow-sm">
                                        <div className="flex items-center justify-between bg-slate-100 px-4 py-2.5">
                                            <button
                                                type="button"
                                                onClick={() => toggleModulo(modulo)}
                                                className="flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-slate-900 flex-1 text-left"
                                            >
                                                <span>{estaAbierto ? '▼' : '►'}</span>
                                                <span>Módulo: {modulo}</span>
                                                <span className="text-xs bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full font-normal">
                                                    {seleccionadosEnModulo} / {permisosModulo.length}
                                                </span>
                                            </button>

                                            <button
                                                type="button"
                                                onClick={() => handleToggleGrupo(permisosModulo)}
                                                className="text-xs text-blue-600 hover:underline font-medium ml-2"
                                            >
                                                {seleccionadosEnModulo === permisosModulo.length ? 'Desmarcar Módulo' : 'Marcar Módulo'}
                                            </button>
                                        </div>

                                        {estaAbierto && (
                                            <div className="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50/50">
                                                {permisosModulo.map((permiso) => (
                                                    <label 
                                                        key={permiso.id} 
                                                        className="flex items-center p-2 bg-white rounded-md border border-slate-200 hover:border-blue-300 shadow-sm cursor-pointer select-none transition text-xs text-slate-700"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            value={permiso.name}
                                                            {...register('permisos')}
                                                            className="w-4 h-4 rounded border-slate-300 mr-2"
                                                        />
                                                        <span className="font-medium break-all">{permiso.name}</span>
                                                    </label>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                );
                            })}

                            {catalogoPermisos.length === 0 && (
                                <p className="text-sm text-slate-400 text-center py-4">
                                    No hay permisos registrados en el sistema.
                                </p>
                            )}
                        </div>
                    )}
                </div>

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
                        disabled={isSubmitting}
                        className="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg shadow-sm transition"
                    >
                        {isSubmitting ? 'Guardando...' : 'Guardar'}
                    </button>
                </div>
            </form>
        </Modal>
    );
}