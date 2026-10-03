<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Activos Fijos</title>
    <style>
        body { 
            font-family: {{ $configuracion->fuente ?? 'sans-serif' }}; 
            font-size: 11px; 
            color: {{ $configuracion->color_texto ?? '#333' }}; 
            margin: 0;
            padding: 20px;
        }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; border-bottom: 2px solid {{ $configuracion->color_primario ?? '#2563eb' }}; padding-bottom: 10px; }
        .logo { max-height: 50px; max-width: 140px; }
        .empresa-info { text-align: right; font-size: 10px; }
        h2 { text-align: center; margin: 0 0 5px 0; color: {{ $configuracion->color_primario ?? '#1a202c' }}; font-size: 15px; }
        .subtitle { text-align: center; font-size: 10px; color: #718096; margin-bottom: 15px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e0; padding: 5px 6px; text-align: left; }
        table.data-table th { background-color: {{ $configuracion->color_primario ?? '#edf2f7' }}; color: #ffffff; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; text-align: center; color: #718096; border-top: 1px solid #cbd5e0; padding-top: 5px; }
    </style>
</head>
<body>

    <!-- Encabezado Institucional con Logo y Datos -->
    <table class="header-table">
        <tr>
            <td style="width: 30%;">
                @if(!empty($configuracion->logo) && file_exists(public_path('storage/' . $configuracion->logo)))
                    <img src="{{ public_path('storage/' . $configuracion->logo) }}" class="logo" alt="Logo">
                @else
                    <h3 style="margin:0; color: {{ $configuracion->color_primario ?? '#000' }};">{{ $configuracion->nombre_sistema ?? 'Sistema' }}</h3>
                @endif
            </td>
            <td style="width: 40%; text-align: center;">
                <span style="font-size: 12px; font-weight: bold; color: #555;">{{ strtoupper($configuracion->nombre_empresa ?? '') }}</span><br>
                @if(!empty($configuracion->nit_empresa)) <span style="font-size: 9px;">NIT: {{ $configuracion->nit_empresa }}</span> @endif
            </td>
            <td class="empresa-info" style="width: 30%;">
                @if(!empty($configuracion->telefono)) Tlf: {{ $configuracion->telefono }}<br> @endif
                <span style="font-size: 9px; color: #555;">{{ $configuracion->direccion }}</span>
            </td>
        </tr>
    </table>

    @yield('content')

    <div class="footer">
        {{ $configuracion->nombre_empresa ?? '' }} - Generado por {{ $configuracion->nombre_sistema ?? 'Sistema' }} | Fecha: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>