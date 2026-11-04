import { useEffect, useState, useContext } from 'react'; // 🚀 Añadimos useContext
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion'; // 🚀 Importamos tu contexto
import Plantilla from '../../componentes/estructura/Plantilla';
import TablaCategorias from './TablaCategorias';
import FormularioCategoria from './FormularioCategoria';
import { servicioCategoria } from '../../servicios/servicioCategoria';
import Swal from 'sweetalert2';

export default function ListaCategorias() {
    // 🚀 Extraemos la función de permisos del contexto
    const { tienePermiso } = useContext(ContextoAutenticacion);

    const [categorias, setCategorias] = useState([]);
    const [mostrarModal, setMostrarModal] = useState(false);
    const [categoriaAEditar, setCategoriaAEditar] = useState(null);
    const [incluirBajas, setIncluirBajas] = useState(false);

    const cargarCategorias = async () => {
        try {
            const datos = await servicioCategoria.listar(incluirBajas);
            setCategorias(datos);
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar las categorías.', 'error');
        }
    };

    useEffect(() => {
        cargarCategorias();
    }, [incluirBajas]);

    const handleEliminar = async (id) => {
        const resultado = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'La categoría será enviada a la papelera (Soft Delete).',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (resultado.isConfirmed) {
            try {
                const res = await servicioCategoria.eliminar(id);
                Swal.fire('Eliminado', res.mensaje || 'Categoría en papelera.', 'success');
                cargarCategorias();
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
            const res = await servicioCategoria.restaurar(id);
            Swal.fire('Restaurado', res.mensaje || 'Categoría activa de nuevo.', 'success');
            cargarCategorias();
        } catch (error) {
            Swal.fire('Error', 'No se pudo restaurar la categoría.', 'error');
        }
    };

    const handleAbrirCrear = () => {
        setCategoriaAEditar(null);
        setMostrarModal(true);
    };

    const handleAbrirEditar = (categoria) => {
        setCategoriaAEditar(categoria);
        setMostrarModal(true);
    };

    return (
        <Plantilla>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Categorías de Activos</h1>
                    <p className="text-sm text-slate-500 mt-1">
                        Configuración de grupos, vidas útiles y depreciaciones
                    </p>
                </div>

                {/* 🚀 CONDICIONAL: Solo si tiene permiso para crear categorías */}
                {tienePermiso('categoria_guardar') && (
                    <button
                        onClick={handleAbrirCrear}
                        className="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        + Nueva Categoría
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
                    Mostrar categorías eliminadas (Papelera)
                </label>
            </div>

            {/* 🚀 PASAMOS tienePermiso A LA TABLA DE CATEGORÍAS */}
            <TablaCategorias
                categorias={categorias}
                onEditar={handleAbrirEditar}
                onEliminar={handleEliminar}
                onRestaurar={handleRestaurar}
                tienePermiso={tienePermiso}
            />

            {mostrarModal && (
                <FormularioCategoria
                    categoria={categoriaAEditar}
                    cerrar={() => setMostrarModal(false)}
                    refrescar={cargarCategorias}
                />
            )}
        </Plantilla>
    );
}