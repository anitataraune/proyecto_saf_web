import { useEffect, useState, useContext } from 'react';
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion';
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaBajas from './TablaBajas';
import FormularioBaja from './FormularioBaja';
import { servicioBaja } from '../../servicios/servicioBaja';
import Swal from 'sweetalert2';

export default function ListaBajas() {
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [bajas, setBajas] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [bajaAEditar, setBajaAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarBajas = async () => {
        try {
            const datos = await servicioBaja.listar(incluirBajas);
            setBajas(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los registros de bajas.', 'error');
        }
    };

    useEffect(() => {
        cargarBajas();
    }, [incluirBajas]);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'El registro de baja será enviado a la papelera (Soft Delete).',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioBaja.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'Baja en papelera.', 'success');
                cargarBajas();
            } catch (error) {
                Swal.fire(
                    'Error',
                    error?.response?.data?.mensaje || 'No se pudo eliminar el registro.',
                    'error'
                );
            }
        }
    };

    const handleRestaurar = async (id) => {
        try {
            const res = await servicioBaja.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Registro de baja activo de nuevo.', 'success');
            cargarBajas();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar el registro.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setBajaAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (baja) => {
        setBajaAEditar(baja);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Bajas de Activos</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Gestión y retiro de activos fijos fuera de servicio
                    </p>
                </div>

                {tienePermiso('baja_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-rose-600 hover:bg-rose-700 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm transition-all"
                    >
                        + Registrar Baja
                    </button>
                )}
            </div>

            <div className="flex items-center mb-4 bg-white px-4 py-3 rounded-xl border border-slate-200">
                <label className="inline-flex items-center cursor-pointer select-none text-slate-700 font-medium text-sm">
                    <input
                        type="checkbox"
                        checked={incluirBajas}
                        onChange={(e) => setIncluirBajas(e.target.checked)}
                        className="w-4 h-4 text-rose-600 bg-slate-100 border-slate-300 rounded focus:ring-rose-500 mr-2"
                    />
                    Mostrar bajas eliminadas (Papelera)
                </label>
            </div>

            <TablaBajas
                bajas={bajas}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioBaja
                    baja={bajaAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarBajas}
                />
            )}
        </Plantilla>
    );
}