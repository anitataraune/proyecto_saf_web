import React from 'react';

// 🚀 Recibimos tienePermiso en las props
export default function TablaPolizas({ polizas, onEditar, onEliminar, onRestaurar, tienePermiso }) {
    const formatearFecha = (fechaRaw) => {
        if (!fechaRaw) return 'S/F';
        const fechaLimpia = fechaRaw.split('T')[0];
        const [anio, mes, dia] = fechaLimpia.split('-');
        return `${dia}/${mes}/${anio}`;
    };

    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Código ID</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">
                            Descripción de la Póliza
                        </th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Fecha Inicio</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Fecha Fin</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {polizas.length === 0 ? (
                        <tr>
                            <td colSpan="5" className="px-6 py-12 text-center text-sm text-slate-400">
                                No hay pólizas de seguro registradas.
                            </td>
                        </tr>
                    ) : (
                        polizas.map((p) => (
                            <tr key={p.id_seguro} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-mono text-sm text-slate-600">
                                    POL-{p.id_seguro}
                                </td>
                                <td className="px-6 py-4 text-sm font-medium text-slate-800">
                                    {p.descripcion}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    {formatearFecha(p.fecha_inicio)}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    {formatearFecha(p.fecha_fin)}
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        
                                        {/* 🚀 El botón Editar solo aparece si el registro está activo y tiene el permiso */}
                                        {!p.deleted_at && tienePermiso('poliza_editar') && (
                                            <button
                                                onClick={() => onEditar(p)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {p.deleted_at ? (
                                            /* 🚀 Botón Restaurar condicional */
                                            tienePermiso('poliza_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(p.id_seguro)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            /* 🚀 Botón Eliminar condicional */
                                            tienePermiso('poliza_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(p.id_seguro)}
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