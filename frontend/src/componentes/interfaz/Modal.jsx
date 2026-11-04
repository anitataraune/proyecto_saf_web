import { useEffect } from 'react';
import Icono from './Icono';

export default function Modal({ titulo, cerrar, children, tamano = 'md' }) {
    // Mapa de clases de ancho máximo de Tailwind
    const tamanos = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        '2xl': 'max-w-2xl',
        '3xl': 'max-w-3xl',
    };

    // Cerrar con Escape y bloquear el scroll de la página de fondo
    useEffect(() => {
        const alPresionar = (e) => {
            if (e.key === 'Escape') cerrar();
        };
        const overflowPrevio = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', alPresionar);
        return () => {
            document.body.style.overflow = overflowPrevio;
            document.removeEventListener('keydown', alPresionar);
        };
    }, [cerrar]);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 animar-fondo">
            <div
                role="dialog"
                aria-modal="true"
                className={`flex max-h-[calc(100vh-2rem)] w-full flex-col rounded-2xl border border-slate-200 bg-white shadow-2xl animar-modal ${tamanos[tamano] || tamanos.md}`}
            >
                <div className="flex shrink-0 items-center justify-between gap-4 border-b border-slate-100 px-6 py-4">
                    <h2 className="text-lg font-semibold text-slate-900">{titulo}</h2>
                    <button
                        onClick={cerrar}
                        type="button"
                        className="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
                        aria-label="Cerrar"
                    >
                        <Icono nombre="cerrar" className="w-5 h-5" />
                    </button>
                </div>
                <div className="overflow-y-auto px-6 py-5">{children}</div>
            </div>
        </div>
    );
}
