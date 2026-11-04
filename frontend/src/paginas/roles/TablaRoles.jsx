import React from 'react';

// 🚀 Recibimos tienePermiso en las props
export default function TablaRoles({ roles, onEditar, onEliminar, tienePermiso }) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-sm font-semibold w-24">ID</th>
                        <th className="px-6 py-3 text-sm font-semibold w-1/4">Nombre del Rol</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Permisos Asignados</th>
                        <th className="px-6 py-3 text-sm font-semibold text-center w-40">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {roles.length === 0 ? (
                        <tr>
                            <td colSpan="4" className="px-6 py-12 text-center text-sm text-slate-400">
                                No hay roles registrados en el sistema.
                            </td>
                        </tr>
                    ) : (
                        roles.map((r) => (
                            <tr key={r.id} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-mono text-sm text-slate-600">
                                    #{r.id}
                                </td>
                                <td className="px-6 py-4 text-sm font-bold text-slate-800">
                                    <span className="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md border border-slate-200">
                                        {r.name}
                                    </span>
                                </td>
                                <td className="px-6 py-4 text-sm">
                                    <div className="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto">
                                        {r.permissions && r.permissions.length > 0 ? (
                                            r.permissions.map((p) => (
                                                <span 
                                                    key={p.id} 
                                                    className="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 font-medium rounded-full border border-blue-100"
                                                >
                                                    {p.name}
                                                </span>
                                            ))
                                        ) : (
                                            <span className="text-slate-400 italic text-xs">Sin permisos asignados</span>
                                        )}
                                    </div>
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        
                                        {/* 🚀 Botón Editar condicional */}
                                        {tienePermiso('rol_editar') && (
                                            <button
                                                onClick={() => onEditar(r)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {/* 🚀 Botón Eliminar condicional */}
                                        {tienePermiso('rol_eliminar') && (
                                            <button
                                                onClick={() => onEliminar(r.id)}
                                                className="bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Eliminar
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