import { useContext } from 'react';
import { Navigate, Outlet } from 'react-router-dom';
import { ContextoAutenticacion } from '../contexto/ContextoAutenticacion';

export default function RutaProtegida({ permisoRequerido }) {
    const { autenticado, tienePermiso } = useContext(ContextoAutenticacion);

    // 1. Si no está logueado, lo mandamos al login
    if (!autenticado) {
        return <Navigate to="/login" replace />;
    }

    // 2. Si la ruta exige un permiso y el usuario no lo tiene, lo mandamos al panel
    if (permisoRequerido && !tienePermiso(permisoRequerido)) {
        return <Navigate to="/panel" replace />;
    }

    // 3. Si todo está en orden, renderizamos la vista
    return <Outlet />;
}