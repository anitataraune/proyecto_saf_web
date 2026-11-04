import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioResponsable } from '../../servicios/servicioResponsable';
import { reglasResponsable } from './validacionesResponsable';
import Swal from 'sweetalert2';

export default function FormularioResponsable({ responsable, cerrar, refrescar }) {
    const isEditMode = !!responsable;

    const {
        register,
        handleSubmit,
        setValue,
        setError, // Para inyectar errores provenientes de Laravel (422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario escribe
        defaultValues: {
            nombre_responsable: '',
            apellido_responsable: '',
            cargo_responsable: '',
        },
    });

    useEffect(() => {
        if (isEditMode) {
            setValue('nombre_responsable', responsable.nombre_responsable);
            setValue('apellido_responsable', responsable.apellido_responsable);
            setValue('cargo_responsable', responsable.cargo_responsable);
        }
    }, [responsable, isEditMode, setValue]);

    const onSubmit = async (data) => {
        try {
            if (isEditMode) {
                const res = await servicioResponsable.actualizar(responsable.id_responsable, data);
                Swal.fire('Actualizado', res.mensaje || 'Responsable modificado.', 'success');
            } else {
                const res = await servicioResponsable.guardar(data);
                Swal.fire('Guardado', res.mensaje || 'Responsable registrado.', 'success');
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
                        'No se pudo procesar la información del responsable.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal
            titulo={isEditMode ? '⚙️ Editar Responsable' : '👤 Nuevo Responsable'}
            cerrar={cerrar}
        >
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-1">
                <CampoTexto
                    label="Nombre(s)"
                    name="nombre_responsable"
                    placeholder="Ej. Anita"
                    register={register}
                    errors={errors}
                    validation={reglasResponsable.nombre_responsable}
                />

                <CampoTexto
                    label="Apellido(s)"
                    name="apellido_responsable"
                    placeholder="Ej. Taraune"
                    register={register}
                    errors={errors}
                    validation={reglasResponsable.apellido_responsable}
                />

                <CampoTexto
                    label="Cargo o Puesto de Trabajo"
                    name="cargo_responsable"
                    placeholder="Ej. Administrador de Sistemas"
                    register={register}
                    errors={errors}
                    validation={reglasResponsable.cargo_responsable}
                />

                <div className="flex justify-end gap-2 pt-4 border-t border-slate-100 mt-4">
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