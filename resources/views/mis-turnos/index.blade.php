@extends('adminlte::page')

@section('title', 'Mis turnos')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <h1 class="mb-2">
            <i class="fas fa-calendar-alt text-primary mr-2"></i>
            Mis turnos
        </h1>

        <span class="badge badge-primary p-2">
            <i class="fas fa-user-clock mr-1"></i>
            Consulta personal
        </span>
    </div>
@stop

@section('content')

@php
    $nombreCompleto = trim(implode(' ', array_filter([
        $trabajador->primer_nombre,
        $trabajador->segundo_nombre,
        $trabajador->primer_apellido,
        $trabajador->segundo_apellido,
    ])));

    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
        4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
        10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    $diasSemana = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo',
    ];

    $nombreMes = $meses[$inicioMes->month] . ' de ' . $inicioMes->year;
@endphp

{{-- DATOS DEL TRABAJADOR --}}
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-id-card mr-2"></i>
            Mis datos laborales
        </h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="dato-laboral">
                    <span class="dato-label">Nombres y apellidos</span>
                    <strong>{{ $nombreCompleto ?: 'No especificado' }}</strong>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="dato-laboral">
                    <span class="dato-label">Código</span>
                    <strong>{{ $trabajador->codigo ?: 'No registrado' }}</strong>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="dato-laboral">
                    <span class="dato-label">DNI</span>
                    <strong>{{ $trabajador->dni ?: 'No registrado' }}</strong>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="dato-laboral">
                    <span class="dato-label">Cargo</span>
                    <strong>{{ $trabajador->cargo->nombre ?? 'No especificado' }}</strong>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="dato-laboral">
                    <span class="dato-label">Local asignado</span>
                    <strong>
                        {{ $trabajador->sucursal->nombre ?? 'No especificado' }}
                    </strong>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="dato-laboral">
                    <span class="dato-label">Empresa</span>
                    <strong>
                        {{ $trabajador->empresa->nombre_comercial
                            ?? $trabajador->empresa->nombre
                            ?? 'No especificada' }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CALENDARIO MENSUAL --}}
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header calendario-titulo">
        <div>
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-calendar-alt mr-2"></i>
                Calendario mensual de turnos
            </h3>
            <div class="text-muted small mt-2">
                Los turnos se muestran según la planificación guardada.
            </div>
        </div>
    </div>

    <div class="card-body">

        {{-- NAVEGACIÓN ENTRE MESES --}}
        <div class="navegacion-mes mb-4">
            <a class="btn btn-outline-primary"
               href="{{ route('mis-turnos.index', ['mes' => $mesAnterior]) }}">
                <i class="fas fa-chevron-left mr-1"></i>
                Mes anterior
            </a>

            <h2>{{ $nombreMes }}</h2>

            <a class="btn btn-outline-primary"
               href="{{ route('mis-turnos.index', [
                    'mes' => $mesSiguiente
               ]) }}">
                Mes siguiente
                <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>

        <div class="resumen-semana mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            Cada fecha indica su número de semana ISO. La semana comienza
            el lunes y termina el domingo.
        </div>

        <div class="calendario-scroll">
            <div class="calendario-mensual">

                {{-- ENCABEZADOS --}}
                @foreach ([
                    'Lunes', 'Martes', 'Miércoles', 'Jueves',
                    'Viernes', 'Sábado', 'Domingo'
                ] as $nombreDia)
                    <div class="calendario-encabezado">
                        {{ $nombreDia }}
                    </div>
                @endforeach

                {{-- CUADROS DE CADA FECHA --}}
                @foreach ($dias as $dia)
                    @php
                        $fecha = $dia['fecha'];
                        $turno = $dia['asignacion'];
                        $esHoy = $fecha->isToday();

                        $claseTurno = 'pendiente';
                        $nombreTurno = 'Pendiente';
                        $horasTurno = '';

                        if ($turno) {
                            if ($turno->es_descanso) {
                                $claseTurno = 'descanso';
                                $nombreTurno = 'Descanso';
                            } elseif ($turno->horario) {
                                $horario = $turno->horario;
                                $nombreTurno = $horario->nombre
                                    ?: 'Turno asignado';

                                $entrada = \Carbon\Carbon::parse(
                                    $horario->hora_ingreso
                                )->format('H:i');

                                $salida = \Carbon\Carbon::parse(
                                    $horario->hora_salida
                                )->format('H:i');

                                $horasTurno = $entrada . ' – ' . $salida;

                                if (
                                    strtoupper(trim($horario->tipo_turno ?? ''))
                                        === 'PARTIDO'
                                    && $horario->hora_ingreso2
                                    && $horario->hora_salida2
                                ) {
                                    $entrada2 = \Carbon\Carbon::parse(
                                        $horario->hora_ingreso2
                                    )->format('H:i');

                                    $salida2 = \Carbon\Carbon::parse(
                                        $horario->hora_salida2
                                    )->format('H:i');

                                    $horasTurno .= "\n" . $entrada2
                                        . ' – ' . $salida2;
                                }

                                $horaInicio = \Carbon\Carbon::parse(
                                    $horario->hora_ingreso
                                );

                                $minutosInicio = (
                                    $horaInicio->hour * 60
                                ) + $horaInicio->minute;

                                $horaSalida = \Carbon\Carbon::parse(
                                    $horario->hora_salida
                                );

                                $minutosSalida = (
                                    $horaSalida->hour * 60
                                ) + $horaSalida->minute;

                                $duracion = $minutosSalida - $minutosInicio;

                                if ($duracion <= 0) {
                                    $duracion += 1440;
                                }

                                if (
                                    $duracion >= 600
                                    && $horaInicio->hour >= 7
                                    && $horaInicio->hour <= 9
                                ) {
                                    $claseTurno = 'especial';
                                } elseif ($minutosInicio >= 840) {
                                    $claseTurno = 'tarde';
                                } elseif ($minutosInicio >= 600) {
                                    $claseTurno = 'intermedio';
                                } else {
                                    $claseTurno = 'manana';
                                }
                            } else {
                                $claseTurno = 'sin-horario';
                                $nombreTurno = 'Horario no disponible';
                            }
                        }
                    @endphp

                    <div class="dia-calendario
                        {{ $dia['del_mes'] ? '' : 'dia-otro-mes' }}
                        {{ $esHoy ? 'dia-hoy' : '' }}">

                        <div class="dia-cabecera">
                            <span class="numero-dia">
                                {{ $fecha->format('d') }}
                            </span>

                            @if ($esHoy)
                                <span class="etiqueta-hoy">HOY</span>
                            @endif
                        </div>

                        <div class="numero-semana">
                            Semana {{ $dia['numero_semana'] }}
                            <span>{{ $dia['anio_semana'] }}</span>
                        </div>

                        @if ($turno)
                            <div class="turno-mes {{ $claseTurno }}">
                                <div class="turno-mes-nombre">
                                    @if ($turno->es_descanso)
                                        <i class="fas fa-bed mb-2"></i>
                                    @else
                                        <i class="far fa-clock mb-2"></i>
                                    @endif

                                    <strong>{{ $nombreTurno }}</strong>
                                </div>

                                @if ($horasTurno)
                                    <div class="turno-mes-horas">
                                        {{ $horasTurno }}
                                    </div>
                                @endif

                                @if ($turno->incidencias)
                                    <div class="turno-incidencia">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        {{ $turno->incidencias }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="turno-mes pendiente">
                                <i class="far fa-calendar-minus mb-2"></i>
                                <strong>Pendiente</strong>
                                <small>Sin asignación registrada</small>
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>

        {{-- LEYENDA --}}
        <div class="leyenda-mensual mt-4">
            <span><i class="punto-leyenda manana"></i> Mañana</span>
            <span><i class="punto-leyenda intermedio"></i> Intermedio</span>
            <span><i class="punto-leyenda tarde"></i> Tarde</span>
            <span><i class="punto-leyenda especial"></i> Especial</span>
            <span><i class="punto-leyenda descanso"></i> Descanso</span>
            <span><i class="punto-leyenda pendiente"></i> Pendiente</span>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    .dato-laboral {
        height: 100%;
        padding: 13px 15px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
    }

    .dato-label {
        display: block;
        margin-bottom: 5px;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .dato-laboral strong {
        color: #1e293b;
        overflow-wrap: anywhere;
    }

    .calendario-titulo {
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .navegacion-mes {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .navegacion-mes h2 {
        margin: 0;
        color: #0f172a;
        font-size: 25px;
        font-weight: 800;
        text-align: center;
    }

    .resumen-semana {
        padding: 12px 14px;
        color: #075985;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        font-size: 12px;
    }

    .calendario-scroll {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 8px;
    }

    .calendario-mensual {
        display: grid;
        grid-template-columns: repeat(7, minmax(145px, 1fr));
        min-width: 1050px;
        border-top: 1px solid #dbe4ee;
        border-left: 1px solid #dbe4ee;
        border-radius: 10px;
        overflow: hidden;
        background: white;
    }

    .calendario-encabezado {
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 4px;
        color: #334155;
        background: #f1f5f9;
        border-right: 1px solid #dbe4ee;
        border-bottom: 1px solid #dbe4ee;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .dia-calendario {
        min-width: 0;
        min-height: 205px;
        padding: 9px;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #dbe4ee;
        border-bottom: 1px solid #dbe4ee;
        background: #fff;
    }

    .dia-otro-mes {
        background: #f8fafc;
    }

    .dia-otro-mes .numero-dia {
        color: #94a3b8;
    }

    .dia-hoy {
        background: #eff8ff;
        box-shadow: inset 0 0 0 2px #38bdf8;
    }

    .dia-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 5px;
    }

    .numero-dia {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        color: #334155;
        background: #f1f5f9;
        font-size: 16px;
        font-weight: 800;
    }

    .dia-hoy .numero-dia {
        color: white;
        background: #087f9f;
    }

    .etiqueta-hoy {
        color: #075985;
        background: #dbeafe;
        border-radius: 5px;
        padding: 3px 5px;
        font-size: 9px;
        font-weight: 800;
    }

    .numero-semana {
        margin-top: 7px;
        margin-bottom: 8px;
        color: #64748b;
        font-size: 10px;
        text-align: center;
    }

    .numero-semana span {
        margin-left: 3px;
    }

    .turno-mes {
        flex: 1;
        min-height: 115px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 7px;
        border: 1px solid transparent;
        border-left-width: 4px;
        border-radius: 10px;
        text-align: center;
        overflow-wrap: anywhere;
        white-space: pre-line;
    }

    .turno-mes-nombre {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 12px;
        line-height: 1.4;
    }

    .turno-mes-nombre i {
        font-size: 19px;
    }

    .turno-mes-horas {
        width: 100%;
        padding-top: 8px;
        border-top: 1px solid rgba(100, 116, 139, .25);
        font-size: 12px;
        font-weight: 800;
        line-height: 1.7;
        white-space: pre-line;
    }

    .turno-incidencia {
        width: 100%;
        padding: 5px;
        background: rgba(255,255,255,.65);
        border-radius: 6px;
        font-size: 10px;
        text-align: left;
    }

    .manana {
        color: #1d4ed8;
        background: #dbeafe;
        border-color: #bfdbfe;
        border-left-color: #2563eb;
    }

    .intermedio {
        color: #166534;
        background: #dcfce7;
        border-color: #bbf7d0;
        border-left-color: #16a34a;
    }

    .tarde {
        color: #9a3412;
        background: #ffedd5;
        border-color: #fed7aa;
        border-left-color: #ea580c;
    }

    .especial {
        color: #6b21a8;
        background: #f3e8ff;
        border-color: #e9d5ff;
        border-left-color: #9333ea;
    }

    .descanso {
        color: #475569;
        background: #f1f5f9;
        border-color: #e2e8f0;
        border-left-color: #64748b;
    }

    .pendiente {
        color: #64748b;
        background: #f8fafc;
        border-color: #e2e8f0;
        border-left-color: #cbd5e1;
    }

    .sin-horario {
        color: #991b1b;
        background: #fee2e2;
        border-color: #fecaca;
        border-left-color: #dc2626;
    }

    .turno-mes.pendiente {
        font-size: 12px;
    }

    .turno-mes.pendiente small {
        font-size: 10px;
    }

    .leyenda-mensual {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 20px;
        padding: 15px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }

    .leyenda-mensual span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .punto-leyenda {
        display: inline-block;
        width: 13px;
        height: 13px;
        border: 1px solid currentColor;
        border-radius: 4px;
    }

    .punto-leyenda.manana { background: #dbeafe; color: #2563eb; }
    .punto-leyenda.intermedio { background: #dcfce7; color: #16a34a; }
    .punto-leyenda.tarde { background: #ffedd5; color: #ea580c; }
    .punto-leyenda.especial { background: #f3e8ff; color: #9333ea; }
    .punto-leyenda.descanso { background: #f1f5f9; color: #64748b; }
    .punto-leyenda.pendiente { background: #f8fafc; color: #cbd5e1; }

    @media (max-width: 767.98px) {
        .navegacion-mes {
            justify-content: center;
        }

        .navegacion-mes h2 {
            width: 100%;
            order: -1;
            font-size: 22px;
        }
    }
</style>
@stop
