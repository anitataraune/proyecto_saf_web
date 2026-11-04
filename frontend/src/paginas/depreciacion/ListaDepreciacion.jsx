import { useEffect, useState, useContext } from 'react';
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion';
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaDepreciacion from './TablaDepreciacion';
import { servicioDepreciacion } from '../../servicios/servicioDepreciacion';
import Swal from 'sweetalert2';

export default function ListaDepreciacion() {
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [depreciaciones, setDepreciaciones] = useState([]);
    const [incluirBajas, setIncluirBajas] = useState(false);
    const [procesando, setProcesando] = useState(false);

    const cargarDepreciaciones = async () => {
        try {
            const datos = await servicioDepreciacion.listar(null, incluirBajas);
            setDepreciaciones(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los registros de depreciación.', 'error');
        }
    };

    useEffect(() => {
        cargarDepreciaciones();
    }, [incluirBajas]);

    const handleEjecutarProceso = async () => {
        const fechaActual = new Date();
        
        const { value: formValues } = await Swal.fire({
            title: '⚡ Ejecutar Depreciación Mensual',
            html: `
                <div class="flex flex-col gap-3 text-left">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Mes</label>
                        <select id="swal-mes" class="w-full p-2 border rounded-lg text-sm">
                            ${[...Array(12).keys()].map(i => `<option value="${i+1}" ${i === fechaActual.getMonth() ? 'selected' : ''}>Mes ${i+1}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Año</label>
                        <input id="swal-anio" type="number" class="w-full p-2 border rounded-lg text-sm" value="${fechaActual.getFullYear()}" />
                    </div>
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Procesar Ahora',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return {
                    mes: parseInt(document.getElementById('swal-mes').value, 10),
                    anio: parseInt(document.getElementById('swal-anio').value, 10),
                };
            }
        });

        if (formValues) {
            try {
                setProcesando(true);
                const res = await servicioDepreciacion.procesar(formValues.mes, formValues.anio);
                Swal.fire('Completado', `${res.mensaje} (${res.total_procesados} activos procesados)`, 'success');
                cargarDepreciaciones();
            } catch (error) {
                Swal.fire('Error', error?.response?.data?.mensaje || 'Error al ejecutar depreciación.', 'error');
            } finally {
                setProcesando(false);
            }
        }
    };

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'El registro de depreciación será enviado a la papelera.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioDepreciacion.eliminar(id);
                Swal.fire('Eliminado', res.mensaje, 'success');
                cargarDepreciaciones();
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar.', 'error');
            }
        }
    };

    const handleRestaurar = async (id) => {
        try {
            const res = await servicioDepreciacion.restaurar(id);
            Swal.fire('Restaurado', res.mensaje, 'success');
            cargarDepreciaciones();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar.', 'error');
        }
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Depreciación de Activos</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Cálculo y seguimiento del desgaste contable de bienes de la empresa
                    </p>
                </div>

                {tienePermiso('depreciacion_procesar') && (
                    <button
                        onClick={handleEjecutarProceso}
                        disabled={procesando}
                        className="bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm transition-all flex items-center gap-2"
                    >
                        ⚡ {procesando ? 'Procesando...' : 'Ejecutar Depreciación'}
                    </button>
                )}
            </div>

            <div className="flex items-center mb-4 bg-white px-4 py-3 rounded-xl border border-slate-200">
                <label className="inline-flex items-center cursor-pointer select-none text-slate-700 font-medium text-sm">
                    <input
                        type="checkbox"
                        checked={incluirBajas}
                        onChange={(e) => setIncluirBajas(e.target.checked)}
                        className="w-4 h-4 text-indigo-600 bg-slate-100 border-slate-300 rounded focus:ring-indigo-500 mr-2"
                    />
                    Mostrar eliminados (Papelera)
                </label>
            </div>

            <TablaDepreciacion
                depreciaciones={depreciaciones}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                tienePermiso={tienePermiso}
            />
        </Plantilla>
    );
}