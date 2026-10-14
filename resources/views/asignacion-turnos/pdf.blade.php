
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planificación semanal</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1e293b;
        }

        h1 {
            color: #075985;
            font-size: 17px;
            margin: 0 0 5px;
        }

        .subtitulo {
            font-size: 10px;
            margin-bottom: 12px;
        }

        table.calendario {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .calendario th {
            padding: 7px 3px;
            color: #ffffff;
            background: #087f9f;
            border: 1px solid #cbd5e1;
            font-size: 9px;
        }

        .calendario td {
            width: 14.28%;
            vertical-align: top;
            padding: 4px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
        }

        .numero-dia {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .tarjeta {
            padding: 5px;
            margin-bottom: 5px;
            border: 1px solid #cbd5e1;
            border-left: 3px solid #2563eb;
            background: #dbeafe;
            color: #1d4ed8;
            word-wrap: break-word;
        }

        .nombre {
            font-size: 8px;
            font-weight: bold;
        }

        .detalle {
            margin-top: 3px;
            font-size: 7px;
        }

        .manana {
            background: #dbeafe;
            border-left-color: #2563eb;
            color: #1d4ed8;
        }

        .intermedio {
            background: #dcfce7;
            border-left-color: #16a34a;
            color: #166534;
        }

        .tarde {
            background: #ffedd5;
            border-left-color: #ea580c;
            color: #9a3412;
        }

        .especial {
            background: #f3e8ff;
            border-left-color: #9333ea;
            color: #6b21a8;
        }

        .descanso {
            background: #f1f5f9;
            border-left-color: #64748b;
            color: #475569;
        }

        .sin-horario {
            background: #fee2e2;
            border-left-color: #dc2626;
            color: #991b1b;
        }

        .leyenda {
            margin-top: 12px;
            width: 100%;
            border-collapse: collapse;
        }

        .leyenda td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            font-size: 8px;
        }

        .muestra {
            display: inline-block;
            width: 9px;
            height: 9px;
            margin-right: 4px;
        }

        .pie {
            margin-top: 8px;
            color: #64748b;
            font-size: 7px;
        }
    </style>
</head>
<body>
    <h1>Planificación semanal de turnos</h1>

    <div class="subtitulo">
        <strong>Sucursal:</strong> {{ $sucursal->nombre }}
        &nbsp; | &nbsp;
        <strong>Semana:</strong>
        {{ $fechaInicio->format('d/m/Y') }}
        al
        {{ $fechaFin->format('d/m/Y') }}
    </div>

    <table class="calendario">
        <thead>
            <tr>
                @foreach ($dias as $dia)
                    <th>
                        {{ ucfirst(\Carbon\Carbon::parse($dia)->locale('es')->isoFormat('dddd')) }}
                        <br>
                        {{ $dia->format('d/m') }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($dias as $dia)
                    @php
                        $turnosDia = $turnos->get(
                            $dia->toDateString(),
                            collect()
                        );
                    @endphp

                    <td>
                        <div class="numero-dia">{{ $dia->format('d/m/Y') }}</div>

                        @forelse ($turnosDia as $turno)

                    @php
                        $trabajador = $turno->trabajador;

                        $nombre = $trabajador
                            ? trim(implode(' ', array_filter([
                                $trabajador->primer_nombre,
                                $trabajador->segundo_nombre,
                                $trabajador->primer_apellido,
                                $trabajador->segundo_apellido,
                            ])))
                            : 'Trabajador no disponible';

                        $cargo = $turno->cargo->nombre
                            ?? $trabajador?->cargo?->nombre
                            ?? 'Cargo no especificado';

                        $clase = 'sin-horario';
                        $horaTexto = 'Horario no disponible';

                        if ($turno->es_descanso) {
                            $clase = 'descanso';
                            $horaTexto = 'DESCANSO';
                        } elseif ($turno->horario) {
                            $horario = $turno->horario;

                            $entrada = \Carbon\Carbon::parse($horario->hora_ingreso);
                            $salida = \Carbon\Carbon::parse($horario->hora_salida);

                            // Primer bloque del horario
                            $horaTexto = $entrada->format('H:i')
                                . ' - '
                                . $salida->format('H:i');

                            // Segundo bloque para turnos partidos
                            if (
                                strtoupper(trim($horario->tipo_turno ?? '')) === 'PARTIDO'
                                && !empty($horario->hora_ingreso2)
                                && !empty($horario->hora_salida2)
                            ) {
                                $entrada2 = \Carbon\Carbon::parse($horario->hora_ingreso2);
                                $salida2 = \Carbon\Carbon::parse($horario->hora_salida2);

                                $horaTexto .= ' / '
                                    . $entrada2->format('H:i')
                                    . ' - '
                                    . $salida2->format('H:i');
                            }

                            // Clasificación del turno para conservar los colores
                            $minutosInicio = $entrada->hour * 60 + $entrada->minute;
                            $duracion = $salida->diffInMinutes($entrada);

                            if (
                                $duracion >= 600
                                && $entrada->hour >= 7
                                && $entrada->hour <= 9
                            ) {
                                $clase = 'especial';
                            } elseif ($minutosInicio >= 840) {
                                $clase = 'tarde';
                            } elseif ($minutosInicio >= 600) {
                                $clase = 'intermedio';
                            } else {
                                $clase = 'manana';
                            }
                        }
                    @endphp

                            <div class="tarjeta {{ $clase }}">
                                <div class="nombre">{{ $nombre }}</div>
                                <div class="detalle">{{ $cargo }}</div>
                                <div class="detalle">{{ $horaTexto }}</div>

                                @if ($turno->incidencias)
                                    <div class="detalle">
                                        Incidencia: {{ $turno->incidencias }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="detalle">Sin asignaciones</div>
                        @endforelse
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <table class="leyenda">
        <tr>
            <td><span class="muestra" style="background:#2563eb"></span> Mañana</td>
            <td><span class="muestra" style="background:#16a34a"></span> Intermedio</td>
            <td><span class="muestra" style="background:#ea580c"></span> Tarde</td>
            <td><span class="muestra" style="background:#9333ea"></span> Jornada especial</td>
            <td><span class="muestra" style="background:#64748b"></span> Descanso</td>
        </tr>
    </table>

    <div class="pie">
        Documento generado por el Sistema de Asignación de Turnos.
    </div>
</body>
</html>
