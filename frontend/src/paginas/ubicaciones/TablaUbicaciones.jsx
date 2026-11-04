import React from 'react';

// 🚀 Recibimos tienePermiso en las props
export default function TablaUbicaciones({ ubicaciones, onEditar, onEliminar, onRestaurar, tienePermiso }) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Código u ID</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Nombre de Ubicación</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Descripción / Detalles</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {ubicaciones.length === 0 ? (
                        <tr>
                            <td colSpan="4" className="px-6 py-12 text-center text-sm text-slate-400">
                                No hay ubicaciones registradas.
                            </td>
                        </tr>
                    ) : (
                        ubicaciones.map((u) => (
                            <tr key={u.id_ubicacion} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-mono text-sm text-slate-600">
                                    UBI-{u.id_ubicacion}
                                </td>
                                <td className="px-6 py-4 text-sm font-medium text-slate-800">
                                    {u.nombre_ubicacion}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-500">
                                    {u.descripcion_ubicacion || 'Sin descripción'}
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        
                                        {/* 🚀 Botón Editar condicional */}
                                        {!u.deleted_at && tienePermiso('ubicacion_editar') && (
                                            <button
                                                onClick={() => onEditar(u)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {u.deleted_at ? (
                                            /* 🚀 Botón Restaurar condicional */
                                            tienePermiso('ubicacion_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(u.id_ubicacion)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            /* 🚀 Botón Eliminar condicional */
                                            tienePermiso('ubicacion_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(u.id_ubicacion)}
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