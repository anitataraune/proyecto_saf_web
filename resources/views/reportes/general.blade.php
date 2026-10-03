@extends('reportes.layout')

@section('content')
    <h2>Reporte General de Activos Fijos</h2>
    <div class="subtitle">Fecha de emisión: {{ date('d/m/Y H:i') }}</div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Ubicación</th>
                <th class="text-right">Valor Compra</th>
                <th class="text-right">Depreciación</th>
                <th>Estado</th>
                <th>Asignado a</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
                <tr>
                    <td>#{{ $activo->id_activo }}</td>
                    <td>{{ $activo->descripcion }}</td>
                    <td>{{ $activo->categoria->nombre ?? 'N/D' }}</td>
                    <td>{{ $activo->ubicacion->nombre_ubicacion ?? 'N/D' }}</td>
                    <td class="text-right">Bs.{{ number_format($activo->valor_compra, 2) }}</td>
                    <td class="text-right">Bs.{{ number_format($activo->imp_depreciacion ?? 0, 2) }}</td>
                    <td>{{ $activo->estado_actual }}</td>
                    <td>{{ $activo->asignacionActual->responsable->nombre_responsable ?? 'No asignado' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection