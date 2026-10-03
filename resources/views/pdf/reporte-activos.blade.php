<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #1e293b; }
        .header p { margin: 5px 0 0 0; color: #64748b; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; color: #0f172a; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .fecha { text-align: right; font-size: 9px; color: #94a3b8; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="fecha">Fecha de emisión: {{ date('d/m/Y H:i') }}</div>
    <div class="header">
        <h2>{{ $titulo }}</h2>
        @if(isset($subtitulo))
            <p>{{ $subtitulo }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre / Descripción</th>
                <th>Categoría</th>
                <th>Ubicación</th>
                <th>Responsable</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activos as $activo)
                <tr>
                    <td><strong>{{ $activo->codigo }}</strong></td>
                    <td>{{ $activo->nombre }}</td>
                    <td>{{ $activo->categoria->nombre ?? 'N/A' }}</td>
                    <td>{{ $activo->ubicacion->nombre ?? 'N/A' }}</td>
                    <td>{{ $activo->responsable->nombre ?? 'N/A' }}</td>
                    <td>{{ $activo->estado }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 15px;">No se encontraron activos fijos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>