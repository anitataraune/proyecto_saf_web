import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioUbicacion } from '../../servicios/servicioUbicacion';
import { reglasUbicacion } from './validacionesUbicacion';
import Swal from 'sweetalert2';

export default function FormularioUbicacion({ ubicacion, cerrar, refrescar }) {
    const isEditMode = !!ubicacion;

    const {
        register,
        handleSubmit,
        setValue,
        setError, // Para inyectar los errores del backend (422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario escribe
        defaultValues: {
            nombre_ubicacion: '',
            descripcion_ubicacion: '',
        },
    });

    useEffect(() => {
        if (isEditMode) {
            setValue('nombre_ubicacion', ubicacion.nombre_ubicacion);
            setValue('descripcion_ubicacion', ubicacion.descripcion_ubicacion || '');
        }
    }, [ubicacion, isEditMode, setValue]);

    const onSubmit = async (data) => {
        try {
            const datosEnviar = {
                nombre_ubicacion: data.nombre_ubicacion,
                descripcion_ubicacion: data.descripcion_ubicacion.trim() || null,
            };

            if (isEditMode) {
                const res = await servicioUbicacion.actualizar(ubicacion.id_ubicacion, datosEnviar);
                Swal.fire('Actualizado', res.mensaje || 'Ubicación modificada.', 'success');
            } else {
                const res = await servicioUbicacion.guardar(datosEnviar);
                Swal.fire('Guardado', res.mensaje || 'Ubicación registrada.', 'success');
            }
            refrescar();
            cerrar();
        } catch (error) {
            // Manejo de errores de validación provenientes de Laravel (Código HTTP 422)
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
                    error?.response?.data?.mensaje || error?.response?.data?.message || 'No se pudo procesar la ubicación.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal titulo={isEditMode ? '⚙️ Editar Ubicación' : '📍 Nueva Ubicación'} cerrar={cerrar}>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                <CampoTexto
                    label="Nombre de la Ubicación"
                    name="nombre_ubicacion"
                    placeholder="Ej. Planta Alta - Bloque A, Oficina de Sistemas"
                    register={register}
                    errors={errors}
                    validation={reglasUbicacion.nombre_ubicacion}
                />

                <CampoTexto
                    label="Descripción o Notas adicionales (Opcional)"
                    name="descripcion_ubicacion"
                    placeholder="Ej. Almacén central de equipos de computación"
                    register={register}
                    errors={errors}
                    validation={reglasUbicacion.descripcion_ubicacion}
                />

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
                        className="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg shadow-sm transition"
                    >
                        {isSubmitting ? 'Guardando...' : 'Guardar'}
                    </button>
                </div>
            </form>
        </Modal>
    );
}