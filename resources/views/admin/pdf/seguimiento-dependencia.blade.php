<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Seguimiento</title>
    <style>
        @page {
            margin: 90px 35px 60px 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 60px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo {
            height: 42px;
        }

        .title-block {
            text-align: center;
        }

        .title-block h1 {
            font-size: 15px;
            margin: 0 0 4px 0;
        }

        .title-block h2 {
            font-size: 12px;
            margin: 0;
            font-weight: normal;
        }

        .section-title {
            background: #7f1d1d;
            color: white;
            padding: 8px 10px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 8px;
        }

        .info-table,
        .detail-table,
        .capture-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        .detail-table th,
        .detail-table td,
        .capture-table th,
        .capture-table td {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: top;
        }

        .detail-table th,
        .capture-table th {
            background: #f3f4f6;
        }

        .estado {
            font-weight: bold;
        }

        .estado.aprobado {
            color: #166534;
        }

        .estado.observado {
            color: #b91c1c;
        }

        .estado.pendiente {
            color: #a16207;
        }

        .estado.enviado {
            color: #1d4ed8;
        }

        .small {
            font-size: 10px;
            color: #666;
        }

        .box {
            border: 1px solid #ddd;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 10px;
            background: #fafafa;
        }

        .field-line {
            margin-bottom: 3px;
        }
    </style>
</head>

<body>
    <header>
        <table class="header-table">
            <tr>
                <td width="20%">
                    <img src="{{ public_path('sbadmin2/img/sedeco.png') }}" class="logo">
                </td>
                <td width="60%" class="title-block">
                    <h1>{{ $nombreSistema }}</h1>
                    <h2>Reporte detallado de seguimiento por dependencia</h2>
                </td>
                <td width="20%" style="text-align: right;">
                    <img src="{{ public_path('sbadmin2/img/Integra.png') }}" class="logo">
                </td>
            </tr>
        </table>
    </header>

    <main>
        <div class="section-title">Resumen general</div>

        <table class="info-table">
            <tr>
                <td><strong>Dependencia</strong></td>
                <td>{{ $dependencia->nombre_depen }}</td>
                <td><strong>Fecha de generación</strong></td>
                <td>{{ $fechaGeneracion }}</td>
            </tr>
            <tr>
                <td><strong>Total a capturar</strong></td>
                <td>{{ $resumen['total'] }}</td>
                <td><strong>Capturados</strong></td>
                <td>{{ $resumen['capturados'] }}</td>
            </tr>
            <tr>
                <td><strong>Pendientes</strong></td>
                <td>{{ $resumen['pendientes'] }}</td>
                <td><strong>Enviados</strong></td>
                <td>{{ $resumen['enviados'] }}</td>
            </tr>
            <tr>
                <td><strong>Observados</strong></td>
                <td>{{ $resumen['observados'] }}</td>
                <td><strong>Aprobados</strong></td>
                <td>{{ $resumen['aprobados'] }}</td>
            </tr>
            <tr>
                <td><strong>Avance</strong></td>
                <td colspan="3">{{ $resumen['avance'] }}%</td>
            </tr>
        </table>

        <div class="section-title">Detalle de indicadores y metas</div>

        @foreach ($detalle as $item)
            @php
                $estado = strtoupper($item['estado']);
                $estadoClass = 'enviado';
                if ($estado === 'APROBADO') {
                    $estadoClass = 'aprobado';
                } elseif ($estado === 'OBSERVADO') {
                    $estadoClass = 'observado';
                } elseif ($estado === 'PENDIENTE') {
                    $estadoClass = 'pendiente';
                }
            @endphp

            <table class="detail-table">
                <tr>
                    <th width="30%">Indicador</th>
                    <td>{{ $item['indicador'] }}</td>
                </tr>

                @if ($item['meta'])
                    <tr>
                        <th>Meta</th>
                        <td>{{ $item['meta'] }}</td>
                    </tr>
                @endif

                <tr>
                    <th>Estado</th>
                    <td class="estado {{ $estadoClass }}">{{ $estado }}</td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td>{{ $item['fecha'] }}</td>
                </tr>
            </table>

            @if ($estado === 'APROBADO' && $item['captura']->count())
                @php
                    $primeraCaptura = $item['captura']->first();

                    $columnasDinamicas = collect($item['captura'])
                        ->flatMap(function ($cap) {
                            return array_keys(is_array($cap['campos'] ?? null) ? $cap['campos'] : []);
                        })
                        ->unique()
                        ->values();
                @endphp

                <div class="box">
                    <strong>Datos generales de la captura aprobada</strong>
                </div>

                <table class="capture-table">
                    <tbody>
                        <tr>
                            <th width="18%">Folio</th>
                            <td width="32%">{{ $primeraCaptura['folio'] ?? '—' }}</td>
                            <th width="18%">Fecha</th>
                            <td width="32%">{{ $primeraCaptura['fecha'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Periodo</th>
                            <td>{{ $primeraCaptura['periodo'] ?? '—' }}</td>
                            <th>Ejercicio</th>
                            <td>{{ $primeraCaptura['ejercicio'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Fuente</th>
                            <td>{{ $primeraCaptura['fuente'] ?: '—' }}</td>
                            <th>Ámbito</th>
                            <td>{{ $primeraCaptura['ambito'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Origen</th>
                            <td colspan="3">{{ $primeraCaptura['origen'] ?: '—' }}</td>
                        </tr>
                    </tbody>
                </table>

                @php
                    $mostrarValor = collect($item['captura'])->contains(function ($cap) {
                        return !($cap['valor'] === null || $cap['valor'] === '' || (string) $cap['valor'] === '0.0000');
                    });
                @endphp

                <div class="box">
                    <strong>Información capturada</strong>
                </div>

                <table class="capture-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ubicación</th>

                            @if ($mostrarValor)
                                <th>Valor</th>
                            @endif

                            @foreach ($columnasDinamicas as $col)
                                <th>{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item['captura'] as $i => $cap)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $cap['ubicacion'] ?? '—' }}</td>

                                @if ($mostrarValor)
                                    <td>{{ $cap['valor'] === null || $cap['valor'] === '' || (string) $cap['valor'] === '0.0000' ? '—' : $cap['valor'] }}
                                    </td>
                                @endif

                                @foreach ($columnasDinamicas as $col)
                                    <td>{{ data_get($cap['campos'], $col, '—') }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @php
                    $hayCsv = collect($item['captura'])->contains(fn($cap) => !empty($cap['csv']));
                    $hayRaw = collect($item['captura'])->contains(fn($cap) => !empty($cap['raw']));
                @endphp

                @if ($hayCsv || $hayRaw)
                    <div class="box">
                        <strong>Información adicional</strong>

                        @foreach ($item['captura'] as $i => $cap)
                            @if (!empty($cap['csv']) || !empty($cap['raw']))
                                <div style="margin-top: 6px;">
                                    <strong>Fila {{ $i + 1 }}</strong>

                                    @if (!empty($cap['csv']))
                                        <div class="small">
                                            <strong>CSV:</strong>
                                            Mun: {{ $cap['csv']['municipio'] ?? '—' }},
                                            Reg: {{ $cap['csv']['region'] ?? '—' }},
                                            Cve: {{ $cap['csv']['cve_mun'] ?? '—' }}
                                        </div>
                                    @endif

                                    @if (!empty($cap['raw']))
                                        <div class="small">
                                            <strong>Texto:</strong> {{ $cap['raw'] }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endif
        @endforeach
    </main>
</body>

</html>
