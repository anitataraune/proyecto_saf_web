import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioTipoCambio } from '../../servicios/servicioTipoCambio';
import { reglasTipoCambio } from './validacionesTipoCambio';
import Swal from 'sweetalert2';

export default function FormularioTipoCambio({ tipoCambio, cerrar, refrescar }) {
    const isEditMode = !!tipoCambio;

    const {
        register,
        handleSubmit,
        setValue,
        setError, // Para capturar y pintar errores del backend (422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras escribe/selecciona
        defaultValues: {
            fecha: '',
            ufvs: '',
            valor_tc: '',
        },
    });

    useEffect(() => {
        if (isEditMode) {
            // Aseguramos formatear la fecha a YYYY-MM-DD si viene con hora de la BD
            const fechaFormateada = tipoCambio.fecha ? tipoCambio.fecha.split('T')[0] : '';
            setValue('fecha', fechaFormateada);
            setValue('ufvs', tipoCambio.ufvs);
            setValue('valor_tc', tipoCambio.valor_tc);
        }
    }, [tipoCambio, isEditMode, setValue]);

    const onSubmit = async (data) => {
        try {
            if (isEditMode) {
                const res = await servicioTipoCambio.actualizar(tipoCambio.id_tipo_cambio, data);
                Swal.fire('Actualizado', res.mensaje || 'Tipo de cambio modificado.', 'success');
            } else {
                const res = await servicioTipoCambio.guardar(data);
                Swal.fire('Guardado', res.mensaje || 'Tipo de cambio registrado.', 'success');
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
                    error?.response?.data?.mensaje || error?.response?.data?.message || 'No se pudo procesar la información.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal
            titulo={isEditMode ? '⚙️ Editar Tipo de Cambio' : '📊 Nuevo Tipo de Cambio'}
            cerrar={cerrar}
        >
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-1">
                {/* Campo Fecha (Incluido por requerimiento del controlador unique:fecha) */}
                <CampoTexto
                    label="Fecha"
                    name="fecha"
                    type="date"
                    register={register}
                    errors={errors}
                    validation={reglasTipoCambio.fecha}
                />

                <CampoTexto
                    label="Valor de la UFV"
                    name="ufvs"
                    type="number"
                    step="any"
                    placeholder="Ej. 2.45123"
                    register={register}
                    errors={errors}
                    validation={reglasTipoCambio.ufvs}
                />

                <CampoTexto
                    label="Tipo de Cambio (T.C.)"
                    name="valor_tc"
                    type="number"
                    step="any"
                    placeholder="Ej. 6.96"
                    register={register}
                    errors={errors}
                    validation={reglasTipoCambio.valor_tc}
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