import { useEffect, useState, useContext } from 'react'; // 🚀 Añadimos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos tu contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaProveedores from './TablaProveedores';
import FormularioProveedor from './FormularioProveedor';
import { servicioProveedor } from '../../servicios/servicioProveedor';
import Swal from 'sweetalert2';

export default function ListaProveedores() {
    // 🚀 Extraemos la función de permisos del contexto
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [proveedores, setProveedores] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [proveedorAEditar, setProveedorAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarProveedores = async () => {
        try {
            const datos = await servicioProveedor.listar(incluirBajas);
            setProveedores(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los proveedores.', 'error');
        }
    };

    useEffect(() => {
        cargarProveedores();
    }, [incluirBajas]);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'El proveedor será enviado a la papelera.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioProveedor.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'Proveedor en papelera.', 'success');
                cargarProveedores();
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
            const res = await servicioProveedor.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Proveedor activo de nuevo.', 'success');
            cargarProveedores();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar el proveedor.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setProveedorAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (proveedor) => {
        setProveedorAEditar(proveedor);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Proveedores</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Administración de proveedores y asociados comerciales
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para crear proveedores */}
                {tienePermiso('proveedor_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nuevo Proveedor
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
                    Mostrar proveedores eliminados (Papelera)
                </label>
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA */}
            <TablaProveedores
                proveedores={proveedores}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioProveedor
                    proveedor={proveedorAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarProveedores}
                />
            )}
        </Plantilla>
    );
}