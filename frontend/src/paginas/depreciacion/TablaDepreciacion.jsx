import React from 'react';

export default function TablaDepreciacion({ depreciaciones, onEliminar, onRestaurar, tienePermiso }) {
    const nombresMeses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto border border-slate-100">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Periodo</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Activo Fijo</th>
                        <th className="px-6 py-3 text-sm font-semibold text-right">Dep. Periodo</th>
                        <th className="px-6 py-3 text-sm font-semibold text-right">Dep. Acumulada</th>
                        <th className="px-6 py-3 text-sm font-semibold text-right">Valor Neto</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {depreciaciones.length === 0 ? (
                        <tr>
                            <td colSpan="6" className="px-6 py-12 text-center text-sm text-slate-400">
                                No se registran depreciaciones procesadas.
                            </td>
                        </tr>
                    ) : (
                        depreciaciones.map((d) => (
                            <tr key={d.id_depreciacion} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-medium text-slate-800">
                                    {nombresMeses[d.mes - 1]} / {d.anio}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    {d.activo?.descripcion || `Activo #${d.activo_id}`}
                                </td>
                                <td className="px-6 py-4 text-sm text-right font-mono text-amber-600">
                                    Bs. {parseFloat(d.depreciacion_periodo).toFixed(2)}
                                </td>
                                <td className="px-6 py-4 text-sm text-right font-mono text-rose-600">
                                    Bs. {parseFloat(d.depreciacion_acumulada).toFixed(2)}
                                </td>
                                <td className="px-6 py-4 text-sm text-right font-mono text-emerald-600 font-bold">
                                    Bs. {parseFloat(d.valor_neto).toFixed(2)}
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        {d.deleted_at ? (
                                            tienePermiso('depreciacion_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(d.id_depreciacion)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            tienePermiso('depreciacion_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(d.id_depreciacion)}
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