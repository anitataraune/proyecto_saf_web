import React from 'react';

// 🚀 Recibimos tienePermiso en las props
export default function TablaUsuarioRol({ asignaciones, onEditar, onEliminar, tienePermiso }) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-sm font-semibold w-24">ID Usuario</th>
                        <th className="px-6 py-3 text-sm font-semibold w-1/3">Usuario / Empleado</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Roles Asignados</th>
                        <th className="px-6 py-3 text-sm font-semibold text-center w-40">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {asignaciones.length === 0 ? (
                        <tr>
                            <td colSpan="4" className="px-6 py-12 text-center text-sm text-slate-400">
                                No se encontraron asignaciones de roles activas.
                            </td>
                        </tr>
                    ) : (
                        asignaciones.map((asig) => (
                            <tr key={asig.usuario_id} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-mono text-sm text-slate-600">
                                    #{asig.usuario_id}
                                </td>
                                <td className="px-6 py-4 text-sm font-bold text-slate-800">
                                    {asig.usuario_nombre}
                                </td>
                                <td className="px-6 py-4 text-sm">
                                    <div className="flex flex-wrap gap-1.5">
                                        {asig.roles.map((rol) => (
                                            <span 
                                                key={rol.id} 
                                                className="bg-purple-50 text-purple-700 text-xs px-2.5 py-1 font-semibold rounded-md border border-purple-100 uppercase"
                                            >
                                                {rol.name}
                                            </span>
                                        ))}
                                    </div>
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        
                                        {/* 🚀 Botón Editar condicional */}
                                        {tienePermiso('usuario_rol_editar') && (
                                            <button
                                                onClick={() => onEditar(asig)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {/* 🚀 Botón Revocar condicional */}
                                        {tienePermiso('usuario_rol_eliminar') && (
                                            <button
                                                onClick={() => onEliminar(asig.usuario_id)}
                                                className="bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Revocar
                                            </button>
                                        )}

                                    </div>
                                </td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </div>
    );
}