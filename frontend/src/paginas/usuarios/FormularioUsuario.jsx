import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import { servicioUsuario } from '../../servicios/servicioUsuario';
import { obtenerReglasUsuario } from './validacionesUsuario';
import Swal from 'sweetalert2';

export default function FormularioUsuario({ usuario, cerrar, refrescar }) {
    const isEditMode = !!usuario;
    const reglas = obtenerReglasUsuario(isEditMode);

    const {
        register,
        handleSubmit,
        setValue,
        setError, // Para inyectar los errores de Laravel (422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Activa la validación en tiempo real mientras el usuario escribe
        defaultValues: {
            usuario: '',
            clave: '',
        },
    });

    useEffect(() => {
        if (isEditMode) {
            setValue('usuario', usuario.usuario);
            setValue('clave', ''); // La clave inicia vacía en edición
        }
    }, [usuario, isEditMode, setValue]);

    const onSubmit = async (data) => {
        try {
            const datosEnviar = { usuario: data.usuario };

            // Si hay algo escrito en la clave o es modo creación, la añadimos
            if (data.clave && data.clave.trim() !== '') {
                datosEnviar.clave = data.clave;
            }

            if (isEditMode) {
                await servicioUsuario.actualizar(usuario.id_usuario, datosEnviar);
                Swal.fire('Actualizado', 'Usuario modificado correctamente.', 'success');
            } else {
                await servicioUsuario.guardar(datosEnviar);
                Swal.fire('Guardado', 'Usuario creado correctamente.', 'success');
            }

            refrescar();
            cerrar();
        } catch (error) {
            // Manejo de errores de validación provenientes de Laravel (Código HTTP 422)
            if (error?.response?.status === 422 && error?.response?.data?.errors) {
                const erroresBackend = error.response.data.errors;

                // Mapeamos los errores del backend directamente a los inputs del formulario
                Object.keys(erroresBackend).forEach((campo) => {
                    setError(campo, {
                        type: 'server',
                        message: erroresBackend[campo][0], // Muestra el mensaje exacto que manda Laravel
                    });
                });
            } else {
                // Error general inesperado
                Swal.fire(
                    'Error',
                    error?.response?.data?.mensaje || error?.response?.data?.message || 'Hubo un inconveniente al procesar los datos.',
                    'error'
                );
            }
        }
    };

    return (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div className="bg-white p-6 rounded-xl w-full max-w-md shadow-xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
                <div className="flex justify-between items-center mb-5">
                    <h2 className="text-xl font-bold text-slate-800">
                        {isEditMode ? '⚙️ Editar Usuario' : '👥 Nuevo Usuario'}
                    </h2>
                    <button
                        onClick={cerrar}
                        className="text-slate-400 hover:text-slate-600 text-lg"
                    >
                        ✕
                    </button>
                </div>

                <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                    {/* Campo Usuario */}
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">
                            Nombre de Usuario
                        </label>
                        <input
                            type="text"
                            placeholder="Ej. mlopez"
                            className={`w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition ${
                                errors.usuario ? 'border-rose-500 bg-rose-50' : 'border-slate-300'
                            }`}
                            {...register('usuario', reglas.usuario)}
                        />
                        {errors.usuario && (
                            <p className="text-rose-600 text-xs mt-1 font-medium">
                                {errors.usuario.message}
                            </p>
                        )}
                    </div>

                    {/* Campo Contraseña */}
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">
                            Contraseña{' '}
                            {isEditMode && (
                                <span className="text-slate-400 font-normal">
                                    (Dejar en blanco para no cambiar)
                                </span>
                            )}
                        </label>
                        <input
                            type="password"
                            placeholder="••••••••"
                            className={`w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition ${
                                errors.clave ? 'border-rose-500 bg-rose-50' : 'border-slate-300'
                            }`}
                            {...register('clave', reglas.clave)}
                        />
                        {errors.clave && (
                            <p className="text-rose-600 text-xs mt-1 font-medium">
                                {errors.clave.message}
                            </p>
                        )}
                    </div>

                    {/* Botonera del Modal */}
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
            </div>
        </div>
    );
}