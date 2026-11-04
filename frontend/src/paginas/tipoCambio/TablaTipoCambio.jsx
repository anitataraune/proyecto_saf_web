// "2026-03-05" -> "05/03" (día/mes). Se separa el texto para evitar desfases de zona horaria.
const fechaCorta = (fecha) => {
    if (!fecha) return '—';
    const [, mes, dia] = fecha.split('T')[0].split('-');
    return `${dia}/${mes}`;
};

// 🚀 Recibimos tienePermiso en las props
export default function TablaTipoCambio({ tiposCambio, onEditar, onEliminar, onRestaurar, tienePermiso }) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Fecha</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">UFV</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Tipo de Cambio (T.C.)</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {tiposCambio.length === 0 ? (
                        <tr>
                            <td colSpan="4" className="px-6 py-12 text-center text-sm text-slate-400">
                                No hay tipos de cambio registrados.
                            </td>
                        </tr>
                    ) : (
                        tiposCambio.map((tc) => (
                            <tr key={tc.id_tipo_cambio} className="hover:bg-slate-50/70 transition-colors">
                                <td className="px-6 py-4 font-mono text-sm text-slate-600" title={tc.fecha}>
                                    {fechaCorta(tc.fecha)}
                                </td>
                                <td className="px-6 py-4 text-sm font-medium text-slate-800">
                                    {Number(tc.ufvs).toFixed(5)}
                                </td>
                                <td className="px-6 py-4 text-sm font-mono text-emerald-600 font-semibold">
                                    Bs. {Number(tc.valor_tc).toFixed(2)}
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        
                                        {/* 🚀 Botón Editar condicional */}
                                        {!tc.deleted_at && tienePermiso('tipo_cambio_editar') && (
                                            <button
                                                onClick={() => onEditar(tc)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {tc.deleted_at ? (
                                            /* 🚀 Botón Restaurar condicional */
                                            tienePermiso('tipo_cambio_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(tc.id_tipo_cambio)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            /* 🚀 Botón Eliminar condicional */
                                            tienePermiso('tipo_cambio_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(tc.id_tipo_cambio)}
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