import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioActivo } from '../../servicios/servicioActivo';
import { servicioUbicacion } from '../../servicios/servicioUbicacion';
import { reglasActivo } from './validacionesActivo';
import Swal from 'sweetalert2';

export default function FormularioActivo({ activo, cerrar, refrescar }) {
    const isEditMode = !!activo;
    const [categorias, setCategorias] = useState([]);
    const [ubicaciones, setUbicaciones] = useState([]);
    const [proveedores, setProveedores] = useState([]);
    const [cargando, setCargando] = useState(true);

    const {
        register,
        handleSubmit,
        reset,
        setError, // Para capturar e inyectar errores HTTP 422 del backend
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario interactúa
        defaultValues: {
            descripcion: '',
            valor_compra: '',
            fecha_compra: '',
            categoria_id: '',
            ubicacion_id: '',
            proveedor_id: '',
            estado_actual: 'activo',
        },
    });

    useEffect(() => {
        const cargarDatos = async () => {
            setCargando(true);
            try {
                const [cats, ubis, provs] = await Promise.all([
                    servicioActivo.listarCategorias(),
                    servicioUbicacion.listar(),
                    servicioActivo.listarProveedores()
                ]);
                
                setCategorias(cats);
                setUbicaciones(ubis);
                setProveedores(provs);

                if (isEditMode && activo) {
                    reset({
                        descripcion: activo.descripcion || '',
                        valor_compra: activo.valor_compra || '',
                        fecha_compra: activo.fecha_compra?.split('T')[0] || '',
                        categoria_id: activo.categoria_id ? String(activo.categoria_id) : '',
                        ubicacion_id: activo.ubicacion_id ? String(activo.ubicacion_id) : '',
                        proveedor_id: activo.proveedor_id ? String(activo.proveedor_id) : '',
                        estado_actual: activo.estado_actual || 'activo',
                    });
                }
            } catch (error) {
                console.error('Error cargando datos:', error);
                Swal.fire('Error', 'No se pudieron cargar los datos necesarios.', 'error');
            } finally {
                setCargando(false);
            }
        };

        cargarDatos();
    }, [activo, isEditMode, reset]);

    const onSubmit = async (data) => {
        const datosProcesados = {
            ...data,
            valor_compra: parseFloat(data.valor_compra) || 0,
            categoria_id: data.categoria_id ? parseInt(data.categoria_id, 10) : null,
            ubicacion_id: data.ubicacion_id ? parseInt(data.ubicacion_id, 10) : null,
            proveedor_id: data.proveedor_id ? parseInt(data.proveedor_id, 10) : null,
        };

        try {
            if (isEditMode) {
                await servicioActivo.actualizar(activo.id_activo, datosProcesados);
                Swal.fire('Actualizado', 'Activo modificado con éxito.', 'success');
            } else {
                await servicioActivo.guardar(datosProcesados);
                Swal.fire('Guardado', 'Activo registrado con éxito.', 'success');
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
                        'Error al procesar el activo.', 
                    'error'
                );
            }
        }
    };

    return (
        <Modal titulo={isEditMode ? '⚙️ Editar Activo Fijo' : '📦 Registrar Activo Fijo'} cerrar={cerrar} tamano="lg">
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                
                <CampoTexto 
                    label="Descripción" 
                    name="descripcion" 
                    placeholder="Ej. Laptop HP ProBook 450 G8"
                    register={register} 
                    errors={errors} 
                    validation={reglasActivo.descripcion} 
                />

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <CampoTexto 
                        label="Valor (Bs.)" 
                        name="valor_compra" 
                        type="number" 
                        step="0.01"
                        placeholder="Ej. 4500.00"
                        register={register} 
                        errors={errors} 
                        validation={reglasActivo.valor_compra} 
                    />
                    
                    <CampoTexto 
                        label="Fecha de Compra" 
                        name="fecha_compra" 
                        type="date" 
                        register={register} 
                        errors={errors} 
                        validation={reglasActivo.fecha_compra} 
                    />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="flex flex-col gap-1.5">
                        <label className="text-sm font-semibold text-slate-700">
                            Categoría <span className="text-rose-500">*</span>
                        </label>
                        <select 
                            {...register('categoria_id', reglasActivo.categoria_id)} 
                            disabled={cargando}
                            className="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white outline-none hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                        >
                            <option value="">{cargando ? 'Cargando...' : '-- Seleccione categoría --'}</option>
                            {categorias.map(cat => (
                                <option key={cat.id_categoria} value={String(cat.id_categoria)}>
                                    {cat.nombre}
                                </option>
                            ))}
                        </select>
                        {errors.categoria_id && <span className="text-xs text-rose-600 mt-1 block">{errors.categoria_id.message}</span>}
                    </div>

                    <div className="flex flex-col gap-1.5">
                        <label className="text-sm font-semibold text-slate-700">Ubicación (Opcional)</label>
                        <select 
                            {...register('ubicacion_id', reglasActivo.ubicacion_id)} 
                            disabled={cargando}
                            className="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white outline-none hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                        >
                            <option value="">{cargando ? 'Cargando...' : '-- Sin asignar --'}</option>
                            {ubicaciones.map(ubi => (
                                <option key={ubi.id_ubicacion} value={String(ubi.id_ubicacion)}>
                                    {ubi.nombre_ubicacion}
                                </option>
                            ))}
                        </select>
                        {errors.ubicacion_id && <span className="text-xs text-rose-600 mt-1 block">{errors.ubicacion_id.message}</span>}
                    </div>
                </div>

                <div className="flex flex-col gap-1.5">
                    <label className="text-sm font-semibold text-slate-700">Proveedor (Opcional)</label>
                    <select 
                        {...register('proveedor_id', reglasActivo.proveedor_id)} 
                        disabled={cargando}
                        className="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white outline-none hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                    >
                        <option value="">{cargando ? 'Cargando...' : '-- Sin proveedor --'}</option>
                        {proveedores.map(prov => (
                            <option key={prov.id_proveedor} value={String(prov.id_proveedor)}>
                                {prov.nombre} {prov.apellido || ''}
                            </option>
                        ))}
                    </select>
                    {errors.proveedor_id && <span className="text-xs text-rose-600 mt-1 block">{errors.proveedor_id.message}</span>}
                </div>

                {isEditMode && (
                    <div className="flex flex-col gap-1.5">
                        <label className="text-sm font-semibold text-slate-700">Estado Actual</label>
                        <select 
                            {...register('estado_actual', reglasActivo.estado_actual)} 
                            className="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white outline-none hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                        >
                            <option value="activo">Activo</option>
                            <option value="depreciado">Depreciado</option>
                            <option value="dado_de_baja">Dado de Baja</option>
                        </select>
                        {errors.estado_actual && <span className="text-xs text-rose-600 mt-1 block">{errors.estado_actual.message}</span>}
                    </div>
                )}

                <div className="flex justify-end gap-2 pt-4 border-t border-slate-100">
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
                        disabled={isSubmitting || cargando} 
                        className="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg text-sm font-medium shadow-sm transition"
                    >
                        {isSubmitting ? 'Guardando...' : 'Guardar'}
                    </button>
                </div>
            </form>
        </Modal>
    );
}