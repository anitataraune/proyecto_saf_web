import { useContext, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { servicioAutenticacion } from '../servicios/servicioAutenticacion';
import { ContextoAutenticacion } from '../contexto/ContextoAutenticacion';
import { ContextoConfiguracion } from '../contexto/ContextoConfiguracion';
import Swal from 'sweetalert2';
import Icono from '../componentes/interfaz/Icono';

export default function InicioSesion() {
    const navegar = useNavigate();
    const { iniciar } = useContext(ContextoAutenticacion);
    const { config } = useContext(ContextoConfiguracion);

    const [usuario, setUsuario] = useState('');
    const [clave, setClave] = useState('');
    const [cargando, setCargando] = useState(false);

    const enviarFormulario = async (e) => {
        e.preventDefault();
        if (!usuario.trim() || !clave.trim()) {
            return Swal.fire('Atención', 'Por favor, llena todos los campos', 'warning');
        }

        try {
            setCargando(true);
            const respuesta = await servicioAutenticacion.login({ usuario, clave });
            iniciar(respuesta.access_token, respuesta.usuario);
            navegar('/panel');
        } catch (error) {
            console.error('Error completo capturado:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de ingreso',
                text: error?.response?.data?.mensaje || error?.message || 'No se pudo conectar con el servidor.',
            });
        } finally {
            setCargando(false);
        }
    };

    const nombreEmpresa = config?.nombre_empresa || 'Activos Fijos';

    const estiloCampo = {
        borderColor: 'var(--color-borde)',
        backgroundColor: 'var(--color-superficie)',
        color: 'var(--color-texto)',
    };

    return (
        <div className="min-h-screen grid lg:grid-cols-2" style={{ backgroundColor: 'var(--color-fondo)' }}>
            {/* Panel de marca (solo en pantallas grandes) */}
            <div
                className="relative hidden lg:flex flex-col justify-between overflow-hidden p-12 text-white"
                style={{ backgroundColor: 'var(--color-secundario)' }}
            >
                <div
                    className="absolute -right-24 -top-24 h-96 w-96 rounded-full opacity-30 blur-3xl"
                    style={{ backgroundColor: 'var(--color-primario)' }}
                />
                <div
                    className="absolute -bottom-32 -left-20 h-80 w-80 rounded-full opacity-20 blur-3xl"
                    style={{ backgroundColor: 'var(--color-primario)' }}
                />

                <div className="relative flex items-center gap-3">
                    <img
                        src={config?.logo_url || '/logo.png'}
                        onError={(e) => { if (!e.currentTarget.src.endsWith('/logo.png')) e.currentTarget.src = '/logo.png'; }}
                        alt="Logo"
                        className="h-12 w-12 rounded-xl bg-white object-contain p-1.5"
                    />
                    <span className="text-lg font-semibold">{nombreEmpresa}</span>
                </div>

                <div className="relative max-w-md">
                    <h2 className="text-4xl font-bold leading-tight text-white">
                        {config?.nombre_sistema || 'Sistema de Activos Fijos'}
                    </h2>
                    <p className="mt-4 text-base text-white/70">
                        Registro, asignación, depreciación y control de los bienes de la institución en un solo lugar.
                    </p>
                </div>

                <p className="relative text-xs text-white/40">
                    © {new Date().getFullYear()} {nombreEmpresa}
                </p>
            </div>

            {/* Formulario */}
            <div className="flex items-center justify-center p-6">
                <form onSubmit={enviarFormulario} className="w-full max-w-sm">
                    <div className="mb-8">
                        <img
                            src={config?.logo_url || '/logo.png'}
                        onError={(e) => { if (!e.currentTarget.src.endsWith('/logo.png')) e.currentTarget.src = '/logo.png'; }}
                            alt="Logo"
                            className="mb-6 h-16 w-16 object-contain lg:hidden"
                        />
                        <h1 className="text-2xl font-bold" style={{ color: 'var(--color-texto)' }}>
                            Bienvenido
                        </h1>
                        <p className="mt-1 text-sm text-slate-500">
                            Ingresa tus credenciales para acceder a{' '}
                            <span className="lg:hidden">{config?.nombre_sistema || 'Activos Fijos'}</span>
                            <span className="hidden lg:inline">tu cuenta</span>.
                        </p>
                    </div>

                    <div className="mb-4">
                        <label htmlFor="usuario" className="mb-1.5 block text-sm font-medium" style={{ color: 'var(--color-texto)' }}>
                            Usuario
                        </label>
                        <input
                            id="usuario"
                            type="text"
                            autoComplete="username"
                            autoFocus
                            value={usuario}
                            onChange={(e) => setUsuario(e.target.value)}
                            className="w-full rounded-lg border px-3 py-2.5 text-sm placeholder:text-slate-400 focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100"
                            style={estiloCampo}
                            placeholder="Ej. usuario123"
                        />
                    </div>

                    <div className="mb-6">
                        <label htmlFor="clave" className="mb-1.5 block text-sm font-medium" style={{ color: 'var(--color-texto)' }}>
                            Contraseña
                        </label>
                        <input
                            id="clave"
                            type="password"
                            autoComplete="current-password"
                            value={clave}
                            onChange={(e) => setClave(e.target.value)}
                            className="w-full rounded-lg border px-3 py-2.5 text-sm placeholder:text-slate-400 focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100"
                            style={estiloCampo}
                            placeholder="••••••••"
                        />
                    </div>

                    <button
                        disabled={cargando}
                        className="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-60"
                    >
                        {cargando ? (
                            'Ingresando...'
                        ) : (
                            <>
                                Iniciar sesión
                                <Icono nombre="flecha" className="w-4 h-4" strokeWidth={2} />
                            </>
                        )}
                    </button>
                </form>
            </div>
        </div>
    );
}
