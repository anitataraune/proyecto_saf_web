import React from 'react';

export default function TablaBajas({ bajas, onEditar, onEliminar, onRestaurar, tienePermiso }) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto border border-slate-100">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">ID</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Activo Fijo</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Fecha</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Motivo</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {bajas.length === 0 ? (
                        <tr>
                            <td colSpan="5" className="px-6 py-12 text-center text-sm text-slate-400">
                                No se encontraron registros de bajas.
                            </td>
                        </tr>
                    ) : (
                        bajas.map((b) => (
                            <tr key={b.id_baja} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-mono text-sm text-slate-600">
                                    #{b.id_baja}
                                </td>
                                <td className="px-6 py-4 font-medium text-slate-800">
                                    {b.activo?.descripcion || `Activo #${b.activo_id}`}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    {b.fecha_baja ? new Date(b.fecha_baja).toLocaleDateString() : '-'}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    <span className="bg-rose-50 text-rose-700 font-medium px-2.5 py-1 rounded-md border border-rose-100 inline-block">
                                        {b.motivo_baja}
                                    </span>
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        {!b.deleted_at && tienePermiso('baja_actualizar') && (
                                            <button
                                                onClick={() => onEditar(b)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {b.deleted_at ? (
                                            tienePermiso('baja_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(b.id_baja)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            tienePermiso('baja_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(b.id_baja)}
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