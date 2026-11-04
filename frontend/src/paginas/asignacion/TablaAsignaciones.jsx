import React from 'react';

export default function TablaAsignaciones({ asignaciones, onEditar, onEliminar, onRestaurar, onDevolver, tienePermiso }) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto border border-slate-100">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Activo</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Responsable</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Asignado el</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Estado</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {asignaciones.length === 0 ? (
                        <tr>
                            <td colSpan="5" className="px-6 py-12 text-center text-sm text-slate-400">
                                No existen registros de asignación.
                            </td>
                        </tr>
                    ) : (
                        asignaciones.map((a) => (
                            <tr key={a.id_asignacion} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-medium text-slate-800">
                                    {a.activo?.descripcion || `Activo #${a.activo_id}`}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    {a.responsable ? `${a.responsable.nombre_responsable} ${a.responsable.apellido_responsable}` : '-'}
                                    <span className="block text-xs text-slate-400">{a.responsable?.cargo_responsable}</span>
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    {a.fecha_asignacion ? new Date(a.fecha_asignacion).toLocaleDateString() : '-'}
                                </td>
                                <td className="px-6 py-4 text-sm">
                                    {a.estado_asignacion === 'asignado' ? (
                                        <span className="bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-blue-200">
                                            Asignado
                                        </span>
                                    ) : (
                                        <span className="bg-slate-100 text-slate-600 text-xs font-semibold px-2.5 py-1 rounded-full border border-slate-200">
                                            Devuelto ({new Date(a.fecha_devolucion).toLocaleDateString()})
                                        </span>
                                    )}
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        {/* Botón Devolver activo */}
                                        {!a.deleted_at && a.estado_asignacion === 'asignado' && tienePermiso('asignacion_devolver') && (
                                            <button
                                                onClick={() => onDevolver(a)}
                                                className="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1.5 rounded font-medium transition"
                                            >
                                                Devolver
                                            </button>
                                        )}

                                        {!a.deleted_at && tienePermiso('asignacion_editar') && (
                                            <button
                                                onClick={() => onEditar(a)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {a.deleted_at ? (
                                            tienePermiso('asignacion_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(a.id_asignacion)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            tienePermiso('asignacion_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(a.id_asignacion)}
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