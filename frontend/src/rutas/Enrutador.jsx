import { Routes, Route, Navigate } from 'react-router-dom';
import InicioSesion from '../paginas/InicioSesion';
import PanelPrincipal from '../paginas/PanelPrincipal';
import ConfiguracionSistema from '../paginas/ConfiguracionSistema';

import ListaUsuarios from '../paginas/usuarios/ListaUsuarios';
import ListaRoles from '../paginas/roles/ListaRoles';
import ListaUsuarioRol from '../paginas/usuarioRol/ListaUsuarioRol';
import ListaActivos from '../paginas/activos/ListaActivos';
import ListaCategorias from '../paginas/categorias/ListaCategorias';
import ListaResponsables from '../paginas/responsables/ListaResponsables';
import ListaProveedores from '../paginas/proveedores/ListaProveedores';
import ListaUbicaciones from '../paginas/ubicaciones/ListaUbicaciones';
import ListaTipoCambio from '../paginas/tipoCambio/ListaTipoCambio';
import ListaBajas from '../paginas/baja/ListaBajas';
import ListaAsignaciones from '../paginas/asignacion/ListaAsignaciones';
import ListaDepreciacion from '../paginas/depreciacion/ListaDepreciacion';
import ListaPolizas from '../paginas/polizas/ListaPolizas';

import ReporteActivoGeneral from '../paginas/reportes/ReporteActivoGeneral';
import ReporteActivoUbicacion from '../paginas/reportes/ReporteActivoUbicacion';
import ReporteActivoCategoria from '../paginas/reportes/ReporteActivoCategoria';

import RutaProtegida from './RutaProtegida';

export default function Enrutador() {
    return (
        <Routes>
            {/* Ruta Pública */}
            <Route path="/login" element={<InicioSesion />} />

            {/* 🔓 RUTAS PROTEGIDAS GENERALES (Acceso para cualquier usuario autenticado) */}
            <Route element={<RutaProtegida />}>
                <Route path="/panel" element={<PanelPrincipal />} />
                <Route path="/configuracion" element={<ConfiguracionSistema />} />
            </Route>

            {/* 🔒 1. MÓDULO SEGURIDAD */}
            <Route element={<RutaProtegida permisoRequerido="usuario_listar" />}>
                <Route path="/usuarios" element={<ListaUsuarios />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="rol_listar" />}>
                <Route path="/roles" element={<ListaRoles />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="usuario_rol_listar" />}>
                <Route path="/usuarios-roles" element={<ListaUsuarioRol />} />
            </Route>

            {/* 🔒 2. MÓDULO PARAMETRIZACIÓN */}
            <Route element={<RutaProtegida permisoRequerido="activo_listar" />}>
                <Route path="/activos" element={<ListaActivos />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="categoria_listar" />}>
                <Route path="/categorias" element={<ListaCategorias />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="responsable_listar" />}>
                <Route path="/responsables" element={<ListaResponsables />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="proveedor_listar" />}>
                <Route path="/proveedores" element={<ListaProveedores />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="ubicacion_listar" />}>
                <Route path="/ubicaciones" element={<ListaUbicaciones />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="tipo_cambio_listar" />}>
                <Route path="/tipos-cambio" element={<ListaTipoCambio />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="baja_listar" />}>
                <Route path="/bajas" element={<ListaBajas />} />
            </Route>

            {/* 🔒 3. MÓDULO TRANSACCIÓN */}
            <Route element={<RutaProtegida permisoRequerido="asignacion_listar" />}>
                <Route path="/asignaciones" element={<ListaAsignaciones />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="depreciacion_listar" />}>
                <Route path="/depreciaciones" element={<ListaDepreciacion />} />
            </Route>
            <Route element={<RutaProtegida permisoRequerido="poliza_seguro_listar" />}>
                <Route path="/polizas" element={<ListaPolizas />} />
            </Route>

            {/* 🔒 4. MÓDULO REPORTES */}
            <Route element={<RutaProtegida permisoRequerido="reporte_activo_listar" />}>
                <Route path="/reporte-activo" element={<ReporteActivoGeneral />} />
                <Route path="/reporte-activo-ubicacion" element={<ReporteActivoUbicacion />} />
                <Route path="/reporte-activo-categoria" element={<ReporteActivoCategoria />} />
            </Route>

            {/* Redirección por defecto */}
            <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
    );
}