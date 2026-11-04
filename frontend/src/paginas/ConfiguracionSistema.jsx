import { useState, useEffect, useContext } from 'react';
import { ContextoConfiguracion } from '../contexto/ContextoConfiguracion';
import { ContextoAutenticacion } from '../contexto/ContextoAutenticacion';
import Swal from 'sweetalert2';
import Plantilla from '../componentes/estructura/Plantilla';

export default function ConfiguracionSistema() {
    const { config, actualizarConfiguracion } = useContext(ContextoConfiguracion);
    const { tienePermiso } = useContext(ContextoAutenticacion);
    
    // 🚀 Verificación del permiso
    const puedeEditar = tienePermiso('configuracion_actualizar');

    const [formData, setFormData] = useState({
        nombre_sistema: '',
        nombre_empresa: '',
        nit_empresa: '',
        direccion: '',
        telefono: '',
        color_primario: '#2563EB',
        color_secundario: '#1E293B',
        color_fondo: '#F8FAFC',
        color_superficie: '#FFFFFF',
        color_texto: '#0F172A',
        fuente: 'Roboto',
    });

    const [logoPreview, setLogoPreview] = useState(null);
    const [logoArchivo, setLogoArchivo] = useState(null);
    const [guardando, setGuardando] = useState(false);

    useEffect(() => {
        if (config) {
            setFormData({
                nombre_sistema: config.nombre_sistema || '',
                nombre_empresa: config.nombre_empresa || '',
                nit_empresa: config.nit_empresa || '',
                direccion: config.direccion || '',
                telefono: config.telefono || '',
                color_primario: config.color_primario || '#2563EB',
                color_secundario: config.color_secundario || '#1E293B',
                color_fondo: config.color_fondo || '#F8FAFC',
                color_superficie: config.color_superficie || '#FFFFFF',
                color_texto: config.color_texto || '#0F172A',
                fuente: config.fuente || 'Roboto',
            });
            if (config.logo_url) {
                setLogoPreview(config.logo_url);
            }
        }
    }, [config]);

    const handleChange = (e) => {
        if (!puedeEditar) return;
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleLogoChange = (e) => {
        if (!puedeEditar) return;
        const file = e.target.files[0];
        if (file) {
            setLogoArchivo(file);
            setLogoPreview(URL.createObjectURL(file));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!puedeEditar) return;
        
        setGuardando(true);
        try {
            const datosAEnviar = { ...formData };
            if (logoArchivo) {
                datosAEnviar.logo = logoArchivo;
            }

            await actualizarConfiguracion(datosAEnviar);
            Swal.fire('Éxito', 'Configuración guardada y aplicada correctamente.', 'success');
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Ocurrió un error al guardar la configuración.', 'error');
        } finally {
            setGuardando(false);
        }
    };

    return (
        <Plantilla>
            <div className="max-w-5xl mx-auto">
                <h2 className="text-2xl font-bold mb-6" style={{ color: 'var(--color-texto)' }}>
                    Configuración del Sistema
                </h2>

                {!puedeEditar && (
                    <div className="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded">
                        <strong>Modo Lectura:</strong> No tienes los permisos necesarios para modificar la configuración.
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-6">
                    
                    {/* 1. Identidad */}
                    <div className="tarjeta p-6">
                        <h3 className="text-lg font-semibold mb-4 border-b pb-2" style={{ borderColor: 'var(--color-borde)', color: 'var(--color-texto)' }}>
                            Identidad de la Empresa
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm mb-1" style={{ color: 'var(--color-texto)' }}>Nombre del Sistema</label>
                                <input type="text" name="nombre_sistema" value={formData.nombre_sistema} onChange={handleChange} disabled={!puedeEditar} className="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 disabled:bg-slate-100 disabled:text-slate-500" style={{ borderColor: 'var(--color-borde)' }} />
                            </div>
                            <div>
                                <label className="block text-sm mb-1" style={{ color: 'var(--color-texto)' }}>Nombre de la Empresa</label>
                                <input type="text" name="nombre_empresa" value={formData.nombre_empresa} onChange={handleChange} disabled={!puedeEditar} className="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 disabled:bg-slate-100 disabled:text-slate-500" style={{ borderColor: 'var(--color-borde)' }} />
                            </div>
                            <div>
                                <label className="block text-sm mb-1" style={{ color: 'var(--color-texto)' }}>NIT / Identificación</label>
                                <input type="text" name="nit_empresa" value={formData.nit_empresa} onChange={handleChange} disabled={!puedeEditar} className="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 disabled:bg-slate-100 disabled:text-slate-500" style={{ borderColor: 'var(--color-borde)' }} />
                            </div>
                            <div>
                                <label className="block text-sm mb-1" style={{ color: 'var(--color-texto)' }}>Teléfono</label>
                                <input type="text" name="telefono" value={formData.telefono} onChange={handleChange} disabled={!puedeEditar} className="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 disabled:bg-slate-100 disabled:text-slate-500" style={{ borderColor: 'var(--color-borde)' }} />
                            </div>
                        </div>
                    </div>

                    {/* 2. Logo */}
                    <div className="tarjeta p-6">
                        <h3 className="text-lg font-semibold mb-4 border-b pb-2" style={{ borderColor: 'var(--color-borde)', color: 'var(--color-texto)' }}>
                            Logo Institucional
                        </h3>
                        <div className="flex items-center gap-6">
                            <div className="w-32 h-32 border-2 border-dashed rounded-lg flex items-center justify-center bg-slate-50 p-2 overflow-hidden" style={{ borderColor: 'var(--color-borde)' }}>
                                {logoPreview ? (
                                    <img src={logoPreview} alt="Preview" className="max-w-full max-h-full object-contain" />
                                ) : (
                                    <span className="text-xs text-slate-400 text-center">Sin Logo</span>
                                )}
                            </div>
                            <div>
                                <input type="file" accept="image/*" onChange={handleLogoChange} disabled={!puedeEditar} className="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 disabled:opacity-50" />
                                <p className="text-xs text-slate-500 mt-2">Formatos aceptados: JPG, PNG, WEBP (Max. 2MB).</p>
                            </div>
                        </div>
                    </div>

                    {/* 3. Colores */}
                    <div className="tarjeta p-6">
                        <h3 className="text-lg font-semibold mb-4 border-b pb-2" style={{ borderColor: 'var(--color-borde)', color: 'var(--color-texto)' }}>
                            Paleta de Colores
                        </h3>
                        <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                            {[
                                { id: 'color_primario', label: 'Primario' },
                                { id: 'color_secundario', label: 'Secundario (Sidebar)' },
                                { id: 'color_fondo', label: 'Fondo Pantalla' },
                                { id: 'color_superficie', label: 'Superficie (Tarjetas)' },
                                { id: 'color_texto', label: 'Texto General' },
                            ].map((color) => (
                                <div key={color.id} className="flex flex-col">
                                    <label className="text-xs font-medium mb-1" style={{ color: 'var(--color-texto)' }}>{color.label}</label>
                                    <input type="color" name={color.id} value={formData[color.id]} onChange={handleChange} disabled={!puedeEditar} className="w-full h-10 cursor-pointer border rounded disabled:opacity-50" style={{ borderColor: 'var(--color-borde)' }} />
                                    <span className="text-xs text-slate-500 uppercase mt-1">{formData[color.id]}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* 🚀 Botón de Guardar Estándar */}
                    {puedeEditar && (
                        <div className="flex justify-end pt-4 mb-8">
                            <button
                                type="submit"
                                disabled={guardando}
                                className="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg shadow-sm transition"
                            >
                                {guardando ? 'Guardando...' : 'Guardar'}
                            </button>
                        </div>
                    )}
                </form>
            </div>
        </Plantilla>
    );
}