import React from 'react';

// 🚀 Recibimos tienePermiso en las props
export default function TablaUsuarios({
    usuarios,
    onEditar,
    onEliminar,
    onRestaurar,
    onAlternarEstado,
    tienePermiso,
}) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Usuario</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Estado</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {usuarios.length === 0 ? (
                        <tr>
                            <td colSpan="3" className="px-6 py-12 text-center text-sm text-slate-400">
                                No hay registros disponibles
                            </td>
                        </tr>
                    ) : (
                        usuarios.map((u) => (
                            <tr key={u.id_usuario} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-medium text-slate-800">
                                    {u.usuario}
                                </td>
                                <td className="px-6 py-4 text-center">
                                    {/* 🚀 Alternar estado solo si tiene permiso de edición */}
                                    <button
                                        onClick={() => tienePermiso('usuario_editar') && onAlternarEstado(u)}
                                        disabled={!tienePermiso('usuario_editar')}
                                        className={`px-3 py-1 rounded-full text-xs font-bold shadow-sm transition ${
                                            u.activo
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                : 'bg-red-100 text-red-700 hover:bg-red-200'
                                        } ${!tienePermiso('usuario_editar') ? 'cursor-not-allowed opacity-80' : ''}`}
                                    >
                                        {u.activo ? 'Activo' : 'Inactivo'}
                                    </button>
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        
                                        {/* 🚀 Botón Editar condicional */}
                                        {!u.deleted_at && tienePermiso('usuario_editar') && (
                                            <button
                                                onClick={() => onEditar(u)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {u.deleted_at ? (
                                            /* 🚀 Botón Restaurar condicional */
                                            tienePermiso('usuario_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(u.id_usuario)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            /* 🚀 Botón Eliminar condicional */
                                            tienePermiso('usuario_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(u.id_usuario)}
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