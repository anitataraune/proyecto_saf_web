import React from 'react';

export default function TablaActivos({ 
    activos, 
    onEditar, 
    onEliminar, 
    onRestaurar, // 🚀 Recibimos la función restaurar
    tienePermiso 
}) {
    return (
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table className="w-full min-w-max text-left border-collapse">
                <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">ID</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Descripción</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Categoría</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Ubicación</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Valor</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider">Estado</th>
                        <th className="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {activos.length === 0 ? (
                        <tr>
                            <td colSpan="7" className="px-6 py-12 text-center text-sm text-slate-400">
                                No hay activos registrados.
                            </td>
                        </tr>
                    ) : (
                        activos.map((activo) => (
                            <tr 
                                key={activo.id_activo} 
                                className={`hover:bg-slate-50 transition ${activo.deleted_at ? 'bg-red-50' : ''}`}
                            >
                                <td className="px-6 py-4 font-mono text-sm text-slate-600">
                                    {activo.id_activo}
                                </td>
                                <td className="px-6 py-4 font-medium text-slate-800">
                                    {activo.descripcion}
                                    {activo.deleted_at && (
                                        <span className="ml-2 text-xs text-red-600 font-semibold">
                                            (Eliminado)
                                        </span>
                                    )}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    {activo.categoria?.nombre || 'Sin categoría'}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    {activo.ubicacion?.nombre_ubicacion || 'Sin ubicación'}
                                </td>
                                <td className="px-6 py-4 text-sm text-slate-600">
                                    Bs. {activo.valor_compra?.toFixed(2) || '0.00'}
                                </td>
                                <td className="px-6 py-4">
                                    <span className={`px-2 py-1 text-xs rounded-full ${
                                        activo.estado_actual === 'activo' ? 'bg-green-100 text-green-800' :
                                        activo.estado_actual === 'depreciado' ? 'bg-yellow-100 text-yellow-800' :
                                        'bg-red-100 text-red-800'
                                    }`}>
                                        {activo.estado_actual || 'activo'}
                                    </span>
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <div className="flex justify-center gap-2">
                                        {/* 🚀 Botón Editar: solo si NO está eliminado y tiene permiso */}
                                        {!activo.deleted_at && tienePermiso('activo_actualizar') && (
                                            <button
                                                onClick={() => onEditar(activo)}
                                                className="bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                            >
                                                Editar
                                            </button>
                                        )}

                                        {/* 🚀 Botón Restaurar: solo si está eliminado y tiene permiso */}
                                        {activo.deleted_at ? (
                                            tienePermiso('activo_restaurar') && (
                                                <button
                                                    onClick={() => onRestaurar(activo.id_activo)}
                                                    className="bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
                                                >
                                                    Restaurar
                                                </button>
                                            )
                                        ) : (
                                            /* 🚀 Botón Eliminar: solo si NO está eliminado y tiene permiso */
                                            tienePermiso('activo_eliminar') && (
                                                <button
                                                    onClick={() => onEliminar(activo.id_activo)}
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