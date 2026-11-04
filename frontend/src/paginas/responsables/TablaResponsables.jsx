import React from 'react';

// 🚀 Recibimos tienePermiso en las props
export default function TablaResponsables({ responsables, onEditar, onEliminar, onRestaurar, tienePermiso }) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">ID</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Nombre Completo</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Cargo / Función</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {responsables.length === 0 ? (
                        <tr>
                            <td colSpan="4" className="px-6 py-12 text-center text-sm text-slate-400">
                                No hay responsables registrados.
                            </td>
                        </tr>
                    ) : (
                        responsables.map((r) => (
                            <tr key={r.id_responsable} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-mono text-sm text-slate-600">
                                    RESP-{r.id_responsable}
                                </td>
                                <td className="px-6 py-4 text-sm font-medium text-slate-800">
                                    {r.nombre_responsable} {r.apellido_responsable}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    <span className="bg-slate-100 text-slate-700 px-2 py-1 rounded text-xs font-medium">
                                        {r.cargo_responsable}
                                    </span>
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        
                                        {/* 🚀 Botón Editar condicional */}
                                        {!r.deleted_at && tienePermiso('responsable_editar') && (
                                            <button
                                                onClick={() => onEditar(r)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {r.deleted_at ? (
                                            /* 🚀 Botón Restaurar condicional */
                                            tienePermiso('responsable_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(r.id_responsable)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            /* 🚀 Botón Eliminar condicional */
                                            tienePermiso('responsable_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(r.id_responsable)}
                                                    className="bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Eliminar
                                                </button>
                                            )
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