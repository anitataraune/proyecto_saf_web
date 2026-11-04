import { useState } from 'react';
import BarraLateral from './BarraLateral';
import BarraSuperior from './BarraSuperior';

// Cada página monta su propia Plantilla, así que el menú móvil se cierra solo al navegar.
export default function Plantilla({ children }) {
    const [menuAbierto, setMenuAbierto] = useState(false);

    return (
        <div className="flex min-h-screen" style={{ backgroundColor: 'var(--color-fondo)' }}>
            <BarraLateral abierto={menuAbierto} cerrar={() => setMenuAbierto(false)} />

            <div className="flex-1 flex flex-col min-w-0">
                <BarraSuperior abrirMenu={() => setMenuAbierto(true)} />

                <main className="flex-1 p-4 sm:p-6 lg:p-8">
                    <div className="mx-auto w-full max-w-7xl">{children}</div>
                </main>
            </div>
        </div>
    );
}
