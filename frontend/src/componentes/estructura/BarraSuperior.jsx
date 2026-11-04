import { useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../api/axios';
import { ContextoAutenticacion } from '../../contexto/ContextoAutenticacion';
import { ContextoConfiguracion } from '../../contexto/ContextoConfiguracion';
import Icono from '../interfaz/Icono';

export default function BarraSuperior({ abrirMenu }) {
    const navegar = useNavigate();
    const { usuario, cerrar } = useContext(ContextoAutenticacion);
    const { config } = useContext(ContextoConfiguracion);

    const nombreUsuario = usuario?.usuario ?? 'Usuario';
    const iniciales = nombreUsuario.slice(0, 2).toUpperCase();
    const rol = usuario?.es_admin ? 'Administrador' : usuario?.rol || usuario?.roles?.[0];

    const cerrarSesion = async () => {
        try {
            await api.post('/autenticacion/cerrar-sesion');
        } catch (error) {
            console.log(error);
        }
        cerrar();
        navegar('/');
    };

    return (
        <header
            className="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b px-4 sm:px-6 backdrop-blur"
            style={{
                backgroundColor: 'color-mix(in srgb, var(--color-superficie) 90%, transparent)',
                borderColor: 'var(--color-borde)',
            }}
        >
            <div className="flex items-center gap-3 min-w-0">
                <button
                    type="button"
                    onClick={abrirMenu}
                    className="rounded-md p-2 -ml-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                    aria-label="Abrir menú"
                >
                    <Icono nombre="menu" />
                </button>
                <h2 className="truncate text-base sm:text-lg font-semibold" style={{ color: 'var(--color-texto)' }}>
                    {config?.nombre_sistema || 'Sistema de Activos Fijos'}
                </h2>
            </div>

            <div className="flex items-center gap-3 shrink-0">
                <div className="flex items-center gap-3">
                    <div
                        className="flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold text-white"
                        style={{ backgroundColor: 'var(--color-primario)' }}
                    >
                        {iniciales}
                    </div>
                    <div className="hidden sm:block leading-tight">
                        <p className="text-sm font-medium" style={{ color: 'var(--color-texto)' }}>
                            {nombreUsuario}
                        </p>
                        {typeof rol === 'string' && <p className="text-xs text-slate-500">{rol}</p>}
                    </div>
                </div>

                <div className="h-6 w-px bg-slate-200" />

                <button
                    onClick={cerrarSesion}
                    className="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                    title="Cerrar sesión"
                >
                    <Icono nombre="salir" className="w-[18px] h-[18px]" />
                    <span className="hidden sm:inline">Salir</span>
                </button>
            </div>
        </header>
    );
}
