import { useEffect, useState, useContext } from 'react'; // 🚀 Importamos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos el contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaTipoCambio from './TablaTipoCambio';
import FormularioTipoCambio from './FormularioTipoCambio';
import { servicioTipoCambio } from '../../servicios/servicioTipoCambio';
import Swal from 'sweetalert2';

export default function ListaTipoCambio() {
    // 🚀 Extraemos la función de permisos del contexto
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [tiposCambio, setTiposCambio] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [tipoCambioAEditar, setTipoCambioAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarTiposCambio = async () => {
        try {
            const datos = await servicioTipoCambio.listar(incluirBajas);
            setTiposCambio(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar las tasas de cambio.', 'error');
        }
    };

    useEffect(() => {
        cargarTiposCambio();
    }, [incluirBajas]);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'El registro será enviado a la papelera.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioTipoCambio.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'Registro enviado a la papelera.', 'success');
                cargarTiposCambio();
            } catch (error) {
                Swal.fire(
                    'Error',
                    error?.response?.data?.mensaje || 'No se pudo eliminar.',
                    'error'
                );
            }
        }
    };

    const handleRestaurar = async (id) => {
        try {
            const res = await servicioTipoCambio.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Registro activo de nuevo.', 'success');
            cargarTiposCambio();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar el registro.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setTipoCambioAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (tipoCambio) => {
        setTipoCambioAEditar(tipoCambio);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Tipos de Cambio / UFVs</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Indicadores financieros para indexación de activos fijos
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para crear indicadores */}
                {tienePermiso('tipo_cambio_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nuevo Indicador
                    </button>
                )}
            </div>

            <div className="flex items-center mb-4 bg-white px-4 py-3 rounded-xl border border-slate-200">
                <label className="inline-flex items-center cursor-pointer select-none text-slate-700 font-medium text-sm">
                    <input
                        type="checkbox"
                        checked={incluirBajas}
                        onChange={(e) => setIncluirBajas(e.target.checked)}
                        className="w-4 h-4 rounded border-slate-300 mr-2"
                    />
                    Mostrar eliminados (Papelera)
                </label>
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA */}
            <TablaTipoCambio
                tiposCambio={tiposCambio}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioTipoCambio
                    tipoCambio={tipoCambioAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarTiposCambio}
                />
            )}
        </Plantilla>
    );
}