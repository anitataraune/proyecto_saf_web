@extends('reportes.layout')

@section('content')
    <h2>Reporte de Activos Fijos por Ubicación</h2>
    <div class="subtitle">Fecha de emisión: {{ date('d/m/Y H:i') }}</div>

    @foreach($ubicaciones as $ubicacion)
        <h3 style="margin-top: 15px; background: #edf2f7; padding: 4px 8px; font-size: 11px; border-left: 3px solid {{ $configuracion->color_primario ?? '#2563eb' }};">
            Ubicación: {{ $ubicacion->nombre_ubicacion }}
        </h3>
        
        @if($ubicacion->activos->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th class="text-right">Valor Compra</th>
                        <th class="text-right">Depreciación</th>
                        <th>Estado</th>
                        <th>Asignado a</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ubicacion->activos as $activo)
                        <tr>
                            <td>#{{ $activo->id_activo }}</td>
                            <td>{{ $activo->descripcion }}</td>
                            <td>{{ $activo->categoria->nombre ?? 'N/D' }}</td>
                            <td class="text-right">Bs.{{ number_format($activo->valor_compra, 2) }}</td>
                            <td class="text-right">Bs.{{ number_format($activo->imp_depreciacion ?? 0, 2) }}</td>
                            <td>{{ $activo->estado_actual }}</td>
                            <td>{{ $activo->asignacionActual->responsable->nombre_responsable ?? 'No asignado' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="font-style: italic; color: #718096; font-size: 10px; margin-top: 5px;">No hay activos registrados en esta ubicación.</p>
        @endif
    @endforeach
@endsection