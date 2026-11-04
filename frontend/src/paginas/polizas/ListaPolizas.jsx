import { useEffect, useState, useContext } from 'react'; // 🚀 Añadimos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos tu contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaPolizas from './TablaPolizas';
import FormularioPoliza from './FormularioPoliza';
import { servicioPolizaSeguro } from '../../servicios/servicioPolizaSeguro';
import Swal from 'sweetalert2';

export default function ListaPolizas() {
    // 🚀 Extraemos la función de permisos del contexto
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [polizas, setPolizas] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [polizaAEditar, setPolizaAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarPolizas = async () => {
        try {
            const datos = await servicioPolizaSeguro.listar(incluirBajas);
            setPolizas(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar las pólizas.', 'error');
        }
    };

    useEffect(() => {
        cargarPolizas();
    }, [incluirBajas]);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'La póliza será enviada a la papelera.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioPolizaSeguro.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'Póliza enviada a la papelera.', 'success');
                cargarPolizas();
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
            const res = await servicioPolizaSeguro.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Póliza activa de nuevo.', 'success');
            cargarPolizas();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar la póliza.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setPolizaAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (poliza) => {
        setPolizaAEditar(poliza);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Pólizas de Seguro</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Gestión de coberturas y vigencias para los bienes de la empresa
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para crear pólizas */}
                {tienePermiso('poliza_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nueva Póliza
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
                    Mostrar pólizas eliminadas (Papelera)
                </label>
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA */}
            <TablaPolizas
                polizas={polizas}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioPoliza
                    poliza={polizaAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarPolizas}
                />
            )}
        </Plantilla>
    );
}