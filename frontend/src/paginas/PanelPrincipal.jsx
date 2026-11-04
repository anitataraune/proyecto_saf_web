import { useContext, useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import Plantilla from '../componentes/estructura/Plantilla';
import Icono from '../componentes/interfaz/Icono';
import { ContextoAutenticacion } from '../contexto/ContextoAutenticacion';
import { servicioUsuario } from '../servicios/servicioUsuario';
import { servicioCategoria } from '../servicios/servicioCategoria';
import { servicioProveedor } from '../servicios/servicioProveedor';
import { servicioActivo } from '../servicios/servicioActivo';

const tarjetas = [
    { clave: 'usuarios', titulo: 'Usuarios', icono: 'usuarios', ruta: '/usuarios', permiso: 'usuario_listar', cargar: () => servicioUsuario.listar() },
    { clave: 'categorias', titulo: 'Categorías', icono: 'carpeta', ruta: '/categorias', permiso: 'categoria_listar', cargar: () => servicioCategoria.listar() },
    { clave: 'proveedores', titulo: 'Proveedores', icono: 'camion', ruta: '/proveedores', permiso: 'proveedor_listar', cargar: () => servicioProveedor.listar() },
    { clave: 'activos', titulo: 'Activos Fijos', icono: 'caja', ruta: '/activos', permiso: 'activo_listar', cargar: () => servicioActivo.listar() },
];

const contar = (datos) => {
    if (Array.isArray(datos)) return datos.length;
    if (Array.isArray(datos?.data)) return datos.data.length;
    return null;
};

export default function PanelPrincipal() {
    const { usuario, tienePermiso } = useContext(ContextoAutenticacion);
    const [cantidades, setCantidades] = useState({});

    const visibles = tarjetas.filter((t) => tienePermiso(t.permiso));

    useEffect(() => {
        visibles.forEach((t) => {
            t.cargar()
                .then((datos) => setCantidades((prev) => ({ ...prev, [t.clave]: contar(datos) })))
                .catch(() => setCantidades((prev) => ({ ...prev, [t.clave]: null })));
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    return (
        <Plantilla>
            <div className="mb-8">
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">
                    Hola, {usuario?.usuario ?? 'usuario'}
                </h1>
                <p className="mt-1 text-sm text-slate-500">Resumen general del sistema de activos fijos.</p>
            </div>

            {visibles.length > 0 ? (
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    {visibles.map((t) => {
                        const cantidad = cantidades[t.clave];
                        return (
                            <Link
                                key={t.clave}
                                to={t.ruta}
                                className="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"
                            >
                                <div className="flex items-start justify-between">
                                    <p className="text-sm font-medium text-slate-500">{t.titulo}</p>
                                    <div className="rounded-lg bg-blue-50 p-2 text-blue-600">
                                        <Icono nombre={t.icono} className="w-5 h-5" />
                                    </div>
                                </div>
                                {cantidad === undefined ? (
                                    <div className="mt-3 h-9 w-16 animate-pulse rounded bg-slate-100" />
                                ) : (
                                    <p className="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                                        {cantidad ?? '—'}
                                    </p>
                                )}
                                <p className="mt-3 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-blue-600 transition-colors">
                                    Ver detalle
                                    <Icono nombre="flecha" className="w-3.5 h-3.5" strokeWidth={2} />
                                </p>
                            </Link>
                        );
                    })}
                </div>
            ) : (
                <div className="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                    Usa el menú lateral para acceder a los módulos disponibles para tu rol.
                </div>
            )}
        </Plantilla>
    );
}
