import React from 'react';

// 🚀 Pasito 1: Recibimos tienePermiso en las props
export default function TablaCategorias({ categorias, onEditar, onEliminar, onRestaurar, tienePermiso }) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Código</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Nombre de Categoría</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {categorias.length === 0 ? (
                        <tr>
                            <td colSpan="3" className="px-6 py-12 text-center text-sm text-slate-400">
                                No hay categorías registradas.
                            </td>
                        </tr>
                    ) : (
                        categorias.map((c) => (
                            <tr key={c.id_categoria} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-mono text-sm text-slate-600">
                                    {c.codigo || `CAT-${c.id_categoria}`}
                                </td>
                                <td className="px-6 py-4 font-medium text-slate-800">{c.nombre}</td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        
                                        {/* 🚀 Pasito 2: El botón de editar solo aparece si NO está eliminado y tiene el permiso */}
                                        {!c.deleted_at && tienePermiso('categoria_editar') && (
                                            <button
                                                onClick={() => onEditar(c)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {/* Evaluamos si la categoría está en la papelera */}
                                        {c.deleted_at ? (
                                            /* 🚀 Si está eliminada, verificamos permiso para Restaurar */
                                            tienePermiso('categoria_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(c.id_categoria)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            /* 🚀 Si está activa, verificamos permiso para Eliminar */
                                            tienePermiso('categoria_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(c.id_categoria)}
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