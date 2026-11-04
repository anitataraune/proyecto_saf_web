import { useState, useContext } from 'react';
import { NavLink } from 'react-router-dom';
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion';
import { ContextoConfiguracion } from '../../contexto/ContextoConfiguracion';
import Icono from '../interfaz/Icono';

const menu = [
    {
        id: 'seguridad',
        titulo: 'Seguridad',
        icono: 'escudo',
        opciones: [
            { nombre: 'Usuarios', ruta: '/usuarios', permiso: 'usuario_listar' },
            { nombre: 'Roles', ruta: '/roles', permiso: 'rol_listar' },
            { nombre: 'Asignación de Rol', ruta: '/usuarios-roles', permiso: 'usuario_rol_listar' },
        ],
    },
    {
        id: 'parametrizacion',
        titulo: 'Parametrización',
        icono: 'capas',
        opciones: [
            { nombre: 'Activos Fijos', ruta: '/activos', permiso: 'activo_listar' },
            { nombre: 'Categorías', ruta: '/categorias', permiso: 'categoria_listar' },
            { nombre: 'Responsables', ruta: '/responsables', permiso: 'responsable_listar' },
            { nombre: 'Proveedores', ruta: '/proveedores', permiso: 'proveedor_listar' },
            { nombre: 'Ubicaciones', ruta: '/ubicaciones', permiso: 'ubicacion_listar' },
            { nombre: 'UFVs', ruta: '/tipos-cambio', permiso: 'tipo_cambio_listar' },
            { nombre: 'Bajas', ruta: '/bajas', permiso: 'baja_listar' },
        ],
    },
    {
        id: 'transaccion',
        titulo: 'Transacción',
        icono: 'intercambio',
        opciones: [
            { nombre: 'Asignación de Activos', ruta: '/asignaciones', permiso: 'asignacion_listar' },
            { nombre: 'Depreciación', ruta: '/depreciaciones', permiso: 'depreciacion_listar' },
            { nombre: 'Pólizas de Seguro', ruta: '/polizas', permiso: 'poliza_seguro_listar' },
        ],
    },
    {
        id: 'reporte',
        titulo: 'Reportes',
        icono: 'grafico',
        opciones: [
            { nombre: 'General de Activos', ruta: '/reporte-activo', permiso: 'reporte_activo_listar' },
            { nombre: 'Por Ubicación', ruta: '/reporte-activo-ubicacion', permiso: 'reporte_activo_listar' },
            { nombre: 'Por Categoría', ruta: '/reporte-activo-categoria', permiso: 'reporte_activo_listar' },
        ],
    },
];

const claseEnlace = ({ isActive }) =>
    `flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors ${
        isActive ? 'text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white'
    }`;

const estiloEnlace = ({ isActive }) => (isActive ? { backgroundColor: 'var(--color-primario)' } : {});

export default function BarraLateral({ abierto, cerrar }) {
    const { tienePermiso } = useContext(ContextoAutenticacion);
    const { config } = useContext(ContextoConfiguracion);

    const [abiertos, setAbiertos] = useState({
        seguridad: true,
        parametrizacion: true,
        transaccion: true,
        reporte: true,
    });

    const alternar = (grupo) => {
        setAbiertos((prev) => ({ ...prev, [grupo]: !prev[grupo] }));
    };

    const menuFiltrado = menu
        .map((grupo) => ({
            ...grupo,
            opciones: grupo.opciones.filter((opcion) => tienePermiso(opcion.permiso)),
        }))
        .filter((grupo) => grupo.opciones.length > 0);

    const nombreEmpresa = config?.nombre_empresa || 'Activos Fijos';

    return (
        <>
            {/* Fondo oscuro para el menú en móvil */}
            {abierto && (
                <div className="fixed inset-0 z-30 bg-slate-900/50 lg:hidden animar-fondo" onClick={cerrar} />
            )}

            <aside
                className={`fixed inset-y-0 left-0 z-40 flex w-64 flex-col text-white select-none transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 ${
                    abierto ? 'translate-x-0' : '-translate-x-full'
                }`}
                style={{ backgroundColor: 'var(--color-secundario)' }}
            >
                <div className="flex items-center gap-3 px-5 h-16 border-b border-white/10 shrink-0">
                    <img
                        src={config?.logo_url || '/logo.png'}
                        onError={(e) => { if (!e.currentTarget.src.endsWith('/logo.png')) e.currentTarget.src = '/logo.png'; }}
                        alt="Logo"
                        className="h-10 w-10 shrink-0 rounded-lg bg-white object-contain p-1"
                    />
                    <span className="flex-1 truncate font-semibold leading-tight" title={nombreEmpresa}>
                        {nombreEmpresa}
                    </span>
                    <button
                        type="button"
                        onClick={cerrar}
                        className="rounded-md p-1 text-white/70 hover:bg-white/10 hover:text-white lg:hidden"
                        aria-label="Cerrar menú"
                    >
                        <Icono nombre="cerrar" />
                    </button>
                </div>

                <nav className="scroll-fino flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    <NavLink to="/panel" className={claseEnlace} style={estiloEnlace}>
                        <Icono nombre="inicio" className="w-[18px] h-[18px]" />
                        Panel Principal
                    </NavLink>
                    <NavLink to="/configuracion" className={claseEnlace} style={estiloEnlace}>
                        <Icono nombre="ajustes" className="w-[18px] h-[18px]" />
                        Configuración
                    </NavLink>

                    {menuFiltrado.map((grupo) => (
                        <div key={grupo.id} className="pt-4">
                            <button
                                type="button"
                                onClick={() => alternar(grupo.id)}
                                aria-expanded={abiertos[grupo.id]}
                                className="flex w-full items-center gap-2 rounded-md px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-white/45 hover:text-white/80 transition-colors"
                            >
                                <Icono nombre={grupo.icono} className="w-3.5 h-3.5" strokeWidth={2} />
                                <span className="flex-1 text-left">{grupo.titulo}</span>
                                <Icono
                                    nombre="chevron"
                                    className={`w-3.5 h-3.5 transition-transform ${abiertos[grupo.id] ? '' : '-rotate-90'}`}
                                    strokeWidth={2}
                                />
                            </button>

                            {abiertos[grupo.id] && (
                                <div className="mt-1 ml-[18px] space-y-0.5 border-l border-white/10 pl-3">
                                    {grupo.opciones.map((opcion) => (
                                        <NavLink
                                            key={opcion.ruta}
                                            to={opcion.ruta}
                                            className={({ isActive }) =>
                                                `block rounded-md px-3 py-1.5 text-sm transition-colors ${
                                                    isActive
                                                        ? 'text-white font-medium'
                                                        : 'text-white/65 hover:bg-white/10 hover:text-white'
                                                }`
                                            }
                                            style={estiloEnlace}
                                        >
                                            {opcion.nombre}
                                        </NavLink>
                                    ))}
                                </div>
                            )}
                        </div>
                    ))}
                </nav>

                <div className="shrink-0 border-t border-white/10 px-5 py-3 text-[11px] text-white/40">
                    {config?.nombre_sistema || 'Sistema de Activos Fijos'}
                </div>
            </aside>
        </>
    );
}
