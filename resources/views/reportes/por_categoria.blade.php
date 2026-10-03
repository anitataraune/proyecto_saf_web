@extends('reportes.layout')

@section('content')
    <h2>Reporte de Activos Fijos por Categoría</h2>
    <div class="subtitle">Fecha de emisión: {{ date('d/m/Y H:i') }}</div>

    @foreach($categorias as $categoria)
        <h3 style="margin-top: 15px; background: #edf2f7; padding: 4px 8px; font-size: 11px; border-left: 3px solid {{ $configuracion->color_primario ?? '#2563eb' }};">
            Categoría: {{ $categoria->nombre }} (Vida útil: {{ $categoria->vida_util }} años)
        </h3>
        
        @if($categoria->activos->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Ubicación</th>
                        <th class="text-right">Valor Compra</th>
                        <th class="text-right">Depreciación</th>
                        <th>Estado</th>
                        <th>Asignado a</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoria->activos as $activo)
                        <tr>
                            <td>#{{ $activo->id_activo }}</td>
                            <td>{{ $activo->descripcion }}</td>
                            <td>{{ $activo->ubicacion->nombre_ubicacion ?? 'N/D' }}</td>
                            <td class="text-right">Bs.{{ number_format($activo->valor_compra, 2) }}</td>
                            <td class="text-right">Bs.{{ number_format($activo->imp_depreciacion ?? 0, 2) }}</td>
                            <td>{{ $activo->estado_actual }}</td>
                            <td>{{ $activo->asignacionActual->responsable->nombre_responsable ?? 'No asignado' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="font-style: italic; color: #718096; font-size: 10px; margin-top: 5px;">No hay activos registrados en esta categoría.</p>
        @endif
    @endforeach
@endsection