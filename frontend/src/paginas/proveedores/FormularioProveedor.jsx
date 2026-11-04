import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioProveedor } from '../../servicios/servicioProveedor';
import { reglasProveedor } from './validacionesProveedor';
import Swal from 'sweetalert2';

export default function FormularioProveedor({ proveedor, cerrar, refrescar }) {
    const isEditMode = !!proveedor;

    const {
        register,
        handleSubmit,
        setValue,
        setError, // Para inyectar los errores del backend (por ejemplo, NIT duplicado 422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario escribe
        defaultValues: {
            nombre: '',
            apellido: '',
            nit: '',
            direccion: '',
        },
    });

    useEffect(() => {
        if (isEditMode) {
            setValue('nombre', proveedor.nombre);
            setValue('apellido', proveedor.apellido || '');
            setValue('nit', proveedor.nit || '');
            setValue('direccion', proveedor.direccion || '');
        }
    }, [proveedor, isEditMode, setValue]);

    const onSubmit = async (data) => {
        try {
            const datosEnviar = {
                ...data,
                apellido: data.apellido.trim() || null,
                nit: data.nit.trim() || null,
                direccion: data.direccion.trim() || null,
            };

            if (isEditMode) {
                const res = await servicioProveedor.actualizar(proveedor.id_proveedor, datosEnviar);
                Swal.fire('Actualizado', res.mensaje || 'Proveedor modificado.', 'success');
            } else {
                const res = await servicioProveedor.guardar(datosEnviar);
                Swal.fire('Guardado', res.mensaje || 'Proveedor registrado.', 'success');
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
                        'No se pudo procesar la información del proveedor.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal titulo={isEditMode ? '⚙️ Editar Proveedor' : '🚚 Nuevo Proveedor'} cerrar={cerrar}>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-1">
                <CampoTexto
                    label="Nombre / Razón Social"
                    name="nombre"
                    placeholder="Ej. Importadora Nissan o Juan"
                    register={register}
                    errors={errors}
                    validation={reglasProveedor.nombre}
                />

                <CampoTexto
                    label="Apellido (Opcional)"
                    name="apellido"
                    placeholder="Ej. Pérez (si es persona natural)"
                    register={register}
                    errors={errors}
                    validation={reglasProveedor.apellido}
                />

                <CampoTexto
                    label="NIT (Opcional)"
                    name="nit"
                    placeholder="Ej. 1020304050"
                    register={register}
                    errors={errors}
                    validation={reglasProveedor.nit}
                />

                <CampoTexto
                    label="Dirección (Opcional)"
                    name="direccion"
                    placeholder="Ej. Av. Las Américas Nro 450"
                    register={register}
                    errors={errors}
                    validation={reglasProveedor.direccion}
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