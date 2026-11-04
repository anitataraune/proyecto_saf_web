import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioBaja } from '../../servicios/servicioBaja';
import { reglasBaja } from './validacionesBaja';
import Swal from 'sweetalert2';

export default function FormularioBaja({ baja, cerrar, refrescar }) {
    const isEditMode = !!baja;
    const [activos, setActivos] = useState([]);
    const [cargandoActivos, setCargandoActivos] = useState(false);

    const {
        register,
        handleSubmit,
        setValue,
        setError, // Para inyectar errores de Laravel (422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario escribe/selecciona
        defaultValues: {
            activo_id: '',
            fecha_baja: new Date().toISOString().split('T')[0],
            motivo_baja: '',
            observacion: '',
        },
    });

    useEffect(() => {
        const cargarActivos = async () => {
            try {
                setCargandoActivos(true);
                const datos = await servicioBaja.obtenerActivosDisponibles();
                setActivos(datos);
            } catch (error) {
                Swal.fire('Error', 'No se pudieron cargar los activos disponibles.', 'error');
            } finally {
                setCargandoActivos(false);
            }
        };

        if (!isEditMode) {
            cargarActivos();
        } else {
            setValue('activo_id', baja.activo_id);
            setValue('fecha_baja', baja.fecha_baja ? baja.fecha_baja.split('T')[0] : '');
            setValue('motivo_baja', baja.motivo_baja || '');
            setValue('observacion', baja.observacion || '');
        }
    }, [baja, isEditMode, setValue]);

    const onSubmit = async (data) => {
        try {
            const datosEnviar = {
                ...data,
                activo_id: parseInt(data.activo_id, 10),
                observacion: data.observacion?.trim() || null,
            };

            if (isEditMode) {
                const res = await servicioBaja.actualizar(baja.id_baja, datosEnviar);
                Swal.fire('Actualizado', res.mensaje || 'Registro de baja modificado.', 'success');
            } else {
                const res = await servicioBaja.guardar(datosEnviar);
                Swal.fire('Procesado', res.mensaje || 'Baja de activo registrada.', 'success');
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
                        'No se pudo procesar el registro de baja.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal titulo={isEditMode ? '⚙️ Editar Baja' : '🔻 Registrar Baja de Activo'} cerrar={cerrar}>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                
                {/* Selector de Activo Fijo */}
                <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">
                        Activo Fijo a dar de baja
                    </label>
                    {isEditMode ? (
                        <input
                            type="text"
                            disabled
                            value={baja.activo?.descripcion || `Activo #${baja.activo_id}`}
                            className="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg text-slate-600 text-sm cursor-not-allowed"
                        />
                    ) : (
                        <select
                            {...register('activo_id', reglasBaja.activo_id)}
                            disabled={cargandoActivos}
                            className="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        >
                            <option value="">-- Seleccione un activo --</option>
                            {activos.map((act) => (
                                <option key={act.id_activo} value={act.id_activo}>
                                    {act.descripcion} ({act.estado_actual})
                                </option>
                            ))}
                        </select>
                    )}
                    {errors.activo_id && (
                        <span className="text-xs text-rose-600 mt-1 block">{errors.activo_id.message}</span>
                    )}
                </div>

                <CampoTexto
                    label="Fecha de Baja"
                    name="fecha_baja"
                    type="date"
                    register={register}
                    errors={errors}
                    validation={reglasBaja.fecha_baja}
                />

                <CampoTexto
                    label="Motivo de Baja"
                    name="motivo_baja"
                    placeholder="Ej. Obsolescencia, daño irreparable, hurto..."
                    register={register}
                    errors={errors}
                    validation={reglasBaja.motivo_baja}
                />

                <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">
                        Observaciones (Opcional)
                    </label>
                    <textarea
                        {...register('observacion', reglasBaja.observacion)}
                        rows="3"
                        placeholder="Detalles adicionales sobre el estado final del activo..."
                        className="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none transition"
                    ></textarea>
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
                        disabled={isSubmitting}
                        className="px-4 py-2 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white text-sm font-medium rounded-lg shadow-sm transition"
                    >
                        {isSubmitting ? 'Procesando...' : 'Confirmar Baja'}
                    </button>
                </div>
            </form>
        </Modal>
    );
}