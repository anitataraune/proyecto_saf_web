import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioAsignacion } from '../../servicios/servicioAsignacion';
import { servicioActivo } from '../../servicios/servicioActivo';
import { servicioResponsable } from '../../servicios/servicioResponsable';
import { reglasAsignacion } from './validacionesAsignacion';
import Swal from 'sweetalert2';

export default function FormularioAsignacion({ asignacion, cerrar, refrescar }) {
    const isEditMode = !!asignacion;
    const [activos, setActivos] = useState([]);
    const [responsables, setResponsables] = useState([]);
    const [cargandoAuxiliares, setCargandoAuxiliares] = useState(false);
    const [asignacionCompleta, setAsignacionCompleta] = useState(null);

    const {
        register,
        handleSubmit,
        setValue,
        reset,
        setError, // Para inyectar errores HTTP 422 del backend
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario interactúa
        defaultValues: {
            activo_id: '',
            responsable_id: '',
            fecha_asignacion: new Date().toISOString().split('T')[0],
            observacion: '',
        },
    });

    useEffect(() => {
        const cargarDatos = async () => {
            try {
                setCargandoAuxiliares(true);
                
                // Cargar responsables (siempre)
                const responsablesRes = await servicioResponsable.listar();
                setResponsables(responsablesRes || []);
                
                if (isEditMode && asignacion) {
                    const asignacionData = await servicioAsignacion.mostrar(asignacion.id_asignacion);
                    setAsignacionCompleta(asignacionData);
                    
                    const activosRes = await servicioActivo.listar();
                    setActivos(activosRes || []);
                    
                    reset({
                        activo_id: asignacionData.activo_id || '',
                        responsable_id: asignacionData.responsable_id || '',
                        fecha_asignacion: asignacionData.fecha_asignacion ? asignacionData.fecha_asignacion.split('T')[0] : '',
                        observacion: asignacionData.observacion || '',
                    });
                } else {
                    const activosRes = await servicioActivo.listar();
                    const disponibles = activosRes.filter(activo => 
                        activo.estado_actual === 'activo' && 
                        (!activo.asignacion_actual || activo.asignacion_actual.estado_asignacion !== 'asignado')
                    );
                    setActivos(disponibles || []);
                    
                    reset({
                        activo_id: '',
                        responsable_id: '',
                        fecha_asignacion: new Date().toISOString().split('T')[0],
                        observacion: '',
                    });
                }
            } catch (error) {
                console.error('Error cargando datos:', error);
                Swal.fire('Error', 'No se pudieron cargar los datos necesarios.', 'error');
            } finally {
                setCargandoAuxiliares(false);
            }
        };

        cargarDatos();
    }, [asignacion, isEditMode, reset]);

    const onSubmit = async (data) => {
        try {
            const datosEnviar = {
                ...data,
                activo_id: parseInt(data.activo_id, 10),
                responsable_id: parseInt(data.responsable_id, 10),
                observacion: data.observacion?.trim() || null,
            };

            if (isEditMode) {
                const res = await servicioAsignacion.actualizar(asignacion.id_asignacion, {
                    responsable_id: datosEnviar.responsable_id,
                    fecha_asignacion: datosEnviar.fecha_asignacion,
                    observacion: datosEnviar.observacion,
                });
                Swal.fire('Actualizado', res.mensaje || 'Asignación modificada.', 'success');
            } else {
                const res = await servicioAsignacion.guardar(datosEnviar);
                Swal.fire('Asignado', res.mensaje || 'Activo asignado exitosamente.', 'success');
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
                    error?.response?.data?.mensaje ||
                        error?.response?.data?.message ||
                        'Error al procesar la asignación.',
                    'error'
                );
            }
        }
    };

    const getNombreResponsable = (id) => {
        if (!id) return 'Sin responsable';
        const responsable = responsables.find(r => r.id_responsable === parseInt(id));
        if (responsable) {
            return `${responsable.nombre_responsable} ${responsable.apellido_responsable || ''}`;
        }
        return `Responsable #${id}`;
    };

    return (
        <Modal titulo={isEditMode ? '✏️ Editar Asignación' : '👤 Asignar Activo Fijo'} cerrar={cerrar}>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                
                {!isEditMode && (
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">
                            Activo Fijo <span className="text-red-500">*</span>
                        </label>
                        <select
                            {...register('activo_id', reglasAsignacion.activo_id)}
                            disabled={cargandoAuxiliares}
                            className="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none disabled:bg-slate-50 disabled:cursor-not-allowed"
                        >
                            <option value="">-- Seleccionar Activo --</option>
                            {cargandoAuxiliares ? (
                                <option value="" disabled>Cargando activos...</option>
                            ) : activos.length === 0 ? (
                                <option value="" disabled>No hay activos disponibles</option>
                            ) : (
                                activos.map((act) => (
                                    <option key={act.id_activo} value={act.id_activo}>
                                        {act.descripcion}
                                    </option>
                                ))
                            )}
                        </select>
                        {errors.activo_id && <span className="text-xs text-rose-600 mt-1 block">{errors.activo_id.message}</span>}
                    </div>
                )}

                {isEditMode && (
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">
                            Activo Fijo
                        </label>
                        <div className="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg text-slate-600 text-sm">
                            {asignacionCompleta?.activo?.descripcion || 
                             asignacion.activo?.descripcion || 
                             `Activo #${asignacion.activo_id}`}
                        </div>
                        <p className="text-xs text-slate-400 mt-1">
                            El activo no se puede modificar en edición
                        </p>
                    </div>
                )}

                <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">
                        Responsable / Empleado <span className="text-red-500">*</span>
                    </label>
                    <select
                        {...register('responsable_id', reglasAsignacion.responsable_id)}
                        disabled={cargandoAuxiliares}
                        className="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none disabled:bg-slate-50 disabled:cursor-not-allowed"
                    >
                        <option value="">-- Seleccionar Responsable --</option>
                        {cargandoAuxiliares ? (
                            <option value="" disabled>Cargando responsables...</option>
                        ) : responsables.length === 0 ? (
                            <option value="" disabled>No hay responsables disponibles</option>
                        ) : (
                            responsables.map((r) => (
                                <option key={r.id_responsable} value={r.id_responsable}>
                                    {r.nombre_responsable} {r.apellido_responsable || ''} 
                                    {r.cargo_responsable && ` (${r.cargo_responsable})`}
                                </option>
                            ))
                        )}
                    </select>
                    {errors.responsable_id && <span className="text-xs text-rose-600 mt-1 block">{errors.responsable_id.message}</span>}
                    
                    {isEditMode && asignacion.responsable_id && (
                        <p className="text-xs text-slate-500 mt-1">
                            Responsable actual: <span className="font-medium text-slate-700">
                                {getNombreResponsable(asignacion.responsable_id)}
                            </span>
                        </p>
                    )}
                </div>

                <CampoTexto
                    label="Fecha de Asignación"
                    name="fecha_asignacion"
                    type="date"
                    register={register}
                    errors={errors}
                    validation={reglasAsignacion.fecha_asignacion}
                />

                <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">
                        Observación (Opcional)
                    </label>
                    <textarea
                        {...register('observacion', reglasAsignacion.observacion)}
                        rows="2"
                        placeholder="Ej. Entregado con cargador y bolso..."
                        className="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none resize-none"
                    />
                </div>

                <div className="flex justify-end gap-2 pt-4 mt-2 border-t border-slate-100">
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
                        disabled={isSubmitting || cargandoAuxiliares}
                        className="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg shadow-sm transition"
                    >
                        {isSubmitting ? 'Guardando...' : isEditMode ? 'Actualizar' : 'Asignar'}
                    </button>
                </div>
            </form>
        </Modal>
    );
}