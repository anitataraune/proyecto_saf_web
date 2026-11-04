import { useEffect, useState, useMemo } from 'react';
import { useForm } from 'react-hook-form';
import Modal from '../../componentes/interfaz/Modal';
import CampoTexto from '../../componentes/interfaz/CampoTexto';
import { servicioPolizaSeguro } from '../../servicios/servicioPolizaSeguro';
import { reglasPoliza } from './validacionesPoliza';
import Swal from 'sweetalert2';

export default function FormularioPoliza({ poliza, cerrar, refrescar }) {
    const isEditMode = !!poliza;

    const [activosAgrupados, setActivosAgrupados] = useState({});
    const [cargandoActivos, setCargandoActivos] = useState(true);
    const [categoriasAbiertas, setCategoriasAbiertas] = useState({});

    const {
        register,
        handleSubmit,
        setValue,
        watch,
        setError, // Para inyectar errores de Laravel (422)
        formState: { errors, isSubmitting },
    } = useForm({
        mode: 'onChange', // Validación en tiempo real mientras el usuario interactúa
        defaultValues: {
            descripcion: '',
            fecha_inicio: '',
            fecha_fin: '',
            activos: [],
        },
    });

    const fechaInicioWatch = watch('fecha_inicio');
    const activosSeleccionados = watch('activos') || [];

    // Cargar catálogo de activos agrupados por categoría
    useEffect(() => {
        const cargarCatalogos = async () => {
            try {
                const datos = await servicioPolizaSeguro.listarActivosAgrupados();
                setActivosAgrupados(datos || {});
            } catch (error) {
                Swal.fire('Error', 'No se pudo cargar el catálogo de activos.', 'error');
            } finally {
                setCargandoActivos(false);
            }
        };
        cargarCatalogos();
    }, []);

    // Cargar datos en modo edición
    useEffect(() => {
        if (isEditMode) {
            setValue('descripcion', poliza.descripcion);

            const fechaInicioLimpia = poliza.fecha_inicio ? poliza.fecha_inicio.split('T')[0] : '';
            const fechaFinLimpia = poliza.fecha_fin ? poliza.fecha_fin.split('T')[0] : '';

            setValue('fecha_inicio', fechaInicioLimpia);
            setValue('fecha_fin', fechaFinLimpia);

            if (poliza.activos && Array.isArray(poliza.activos)) {
                const idsActivos = poliza.activos.map((a) => String(a.id_activo));
                setValue('activos', idsActivos);
            }
        }
    }, [poliza, isEditMode, setValue]);

    const totalActivosDisponibles = useMemo(() => {
        return Object.values(activosAgrupados).flat();
    }, [activosAgrupados]);

    const toggleCategoria = (categoria) => {
        setCategoriasAbiertas((prev) => ({
            ...prev,
            [categoria]: !prev[categoria],
        }));
    };

    const handleToggleCategoria = (activosCategoria) => {
        const idsCategoria = activosCategoria.map((a) => String(a.id_activo));
        const todosSeleccionados = idsCategoria.every((id) => activosSeleccionados.includes(id));

        let nuevosActivos;
        if (todosSeleccionados) {
            nuevosActivos = activosSeleccionados.filter((id) => !idsCategoria.includes(id));
        } else {
            nuevosActivos = Array.from(new Set([...activosSeleccionados, ...idsCategoria]));
        }
        setValue('activos', nuevosActivos, { shouldValidate: true });
    };

    const handleToggleTodo = () => {
        if (activosSeleccionados.length === totalActivosDisponibles.length) {
            setValue('activos', [], { shouldValidate: true });
        } else {
            const todosLosIds = totalActivosDisponibles.map((a) => String(a.id_activo));
            setValue('activos', todosLosIds, { shouldValidate: true });
        }
    };

    const onSubmit = async (data) => {
        try {
            const { activos, ...datosPoliza } = data;
            let idSeguro = poliza?.id_seguro;

            if (isEditMode) {
                await servicioPolizaSeguro.actualizar(idSeguro, datosPoliza);
            } else {
                const res = await servicioPolizaSeguro.guardar(datosPoliza);
                idSeguro = res.data.id_seguro;
            }

            await servicioPolizaSeguro.sincronizarActivos(idSeguro, {
                activos: activos.map((id) => Number(id)),
                fecha_asignacion: datosPoliza.fecha_inicio,
            });

            Swal.fire(
                'Éxito',
                isEditMode ? 'Póliza actualizada correctamente.' : 'Póliza registrada con éxito.',
                'success'
            );

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
                        'No se pudo procesar la información de la póliza.',
                    'error'
                );
            }
        }
    };

    return (
        <Modal
            titulo={isEditMode ? '⚙️ Editar Póliza de Seguro' : '🛡️ Nueva Póliza de Seguro'}
            cerrar={cerrar}
            tamano="2xl"
        >
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
                <CampoTexto
                    label="Descripción de la Póliza"
                    name="descripcion"
                    placeholder="Ej. Seguro contra Todo Riesgo - Alianza"
                    register={register}
                    errors={errors}
                    validation={reglasPoliza.descripcion}
                />

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <CampoTexto
                        label="Fecha de Inicio"
                        name="fecha_inicio"
                        type="date"
                        register={register}
                        errors={errors}
                        validation={reglasPoliza.fecha_inicio}
                    />

                    <CampoTexto
                        label="Fecha de Vencimiento"
                        name="fecha_fin"
                        type="date"
                        register={register}
                        errors={errors}
                        validation={{
                            ...reglasPoliza.fecha_fin,
                            validate: (value) =>
                                !fechaInicioWatch ||
                                value >= fechaInicioWatch ||
                                'La fecha de fin no puede ser menor a la de inicio.',
                        }}
                    />
                </div>

                <div>
                    <div className="flex justify-between items-center mb-2">
                        <label className="text-sm font-semibold text-slate-700">
                            Activos Cubiertos ({activosSeleccionados.length} / {totalActivosDisponibles.length})
                        </label>

                        {!cargandoActivos && totalActivosDisponibles.length > 0 && (
                            <button
                                type="button"
                                onClick={handleToggleTodo}
                                className="text-xs text-blue-600 hover:text-blue-800 font-semibold transition"
                            >
                                {activosSeleccionados.length === totalActivosDisponibles.length
                                    ? 'Desmarcar Todos'
                                    : 'Seleccionar Todos'}
                            </button>
                        )}
                    </div>

                    {cargandoActivos ? (
                        <p className="text-sm text-slate-400 py-4 text-center">Cargando catálogo de activos...</p>
                    ) : (
                        <div className="max-h-72 overflow-y-auto space-y-2 pr-1">
                            {Object.keys(activosAgrupados).map((categoria) => {
                                const activosCategoria = activosAgrupados[categoria];
                                const seleccionadosEnCategoria = activosCategoria.filter((a) =>
                                    activosSeleccionados.includes(String(a.id_activo))
                                ).length;
                                const estaAbierto = categoriasAbiertas[categoria] ?? true;

                                return (
                                    <div
                                        key={categoria}
                                        className="border border-slate-200 rounded-lg overflow-hidden bg-white shadow-sm"
                                    >
                                        <div className="flex items-center justify-between bg-slate-100 px-4 py-2.5">
                                            <button
                                                type="button"
                                                onClick={() => toggleCategoria(categoria)}
                                                className="flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-slate-900 flex-1 text-left"
                                            >
                                                <span>{estaAbierto ? '▼' : '►'}</span>
                                                <span>Categoría: {categoria}</span>
                                                <span className="text-xs bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full font-normal">
                                                    {seleccionadosEnCategoria} / {activosCategoria.length}
                                                </span>
                                            </button>

                                            <button
                                                type="button"
                                                onClick={() => handleToggleCategoria(activosCategoria)}
                                                className="text-xs text-blue-600 hover:underline font-medium ml-2"
                                            >
                                                {seleccionadosEnCategoria === activosCategoria.length
                                                    ? 'Desmarcar Categoría'
                                                    : 'Marcar Categoría'}
                                            </button>
                                        </div>

                                        {estaAbierto && (
                                            <div className="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50/50">
                                                {activosCategoria.map((activo) => (
                                                    <label
                                                        key={activo.id_activo}
                                                        className="flex items-center p-2 bg-white rounded-md border border-slate-200 hover:border-blue-300 shadow-sm cursor-pointer select-none transition text-xs text-slate-700"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            value={activo.id_activo}
                                                            {...register('activos')}
                                                            className="w-4 h-4 rounded border-slate-300 mr-2"
                                                        />
                                                        <span className="font-medium truncate" title={activo.descripcion}>
                                                            {activo.descripcion}
                                                        </span>
                                                    </label>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                );
                            })}

                            {totalActivosDisponibles.length === 0 && (
                                <p className="text-sm text-slate-400 text-center py-4">
                                    No hay activos disponibles registrados en el sistema.
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