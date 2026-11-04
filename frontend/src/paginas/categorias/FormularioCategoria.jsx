import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioCategoria } from '../../servicios/servicioCategoria';
import { reglasCategoria } from './validacionesCategoria';
import Swal from 'sweetalert2';

export default function FormularioCategoria({ categoria, cerrar, refrescar }) {
    const isEditMode = !!categoria;

    const {
        register,
        handleSubmit,
        setValue,
        setError, // Para capturar y mapear errores HTTP 422 del backend (ej. nombre duplicado)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario escribe
        defaultValues: {
            nombre: '',
            descripcion: '',
            vida_util: '',
        },
    });

    useEffect(() => {
        if (isEditMode) {
            setValue('nombre', categoria.nombre);
            setValue('descripcion', categoria.descripcion || '');
            setValue('vida_util', categoria.vida_util || '');
        }
    }, [categoria, isEditMode, setValue]);

    const onSubmit = async (data) => {
        try {
            const datosEnviar = {
                ...data,
                vida_util: data.vida_util ? parseInt(data.vida_util, 10) : null,
                descripcion: data.descripcion?.trim() || null,
            };

            if (isEditMode) {
                const res = await servicioCategoria.actualizar(categoria.id_categoria, datosEnviar);
                Swal.fire('Actualizado', res.mensaje || 'Categoría modificada.', 'success');
            } else {
                const res = await servicioCategoria.guardar(datosEnviar);
                Swal.fire('Guardado', res.mensaje || 'Categoría creada.', 'success');
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
                        'La categoría o sus datos contienen errores.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal titulo={isEditMode ? '⚙️ Editar Categoría' : '📁 Nueva Categoría'} cerrar={cerrar}>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                <CampoTexto
                    label="Nombre de la Categoría"
                    name="nombre"
                    placeholder="Ej. Equipos de Computación"
                    register={register}
                    errors={errors}
                    validation={reglasCategoria.nombre}
                />

                <CampoTexto
                    label="Descripción (Opcional)"
                    name="descripcion"
                    placeholder="Ej. Laptops, servidores y periféricos"
                    register={register}
                    errors={errors}
                    validation={reglasCategoria.descripcion}
                />

                <CampoTexto
                    label="Vida Útil en años (Opcional)"
                    name="vida_util"
                    type="number"
                    placeholder="Ej. 5"
                    register={register}
                    errors={errors}
                    validation={reglasCategoria.vida_util}
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