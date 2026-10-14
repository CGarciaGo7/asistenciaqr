@extends('adminlte::page')

@section('title', 'Consultar turnos')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <h1 class="mb-2">
            <i class="fas fa-calendar-alt text-primary"></i>
            Calendario de turnos
        </h1>
        <span class="badge badge-primary p-2">
            <i class="fas fa-calendar-week"></i>
            Planificación semanal
        </span>
    </div>
@stop

@section('content')

{{-- MENSAJES DEL SISTEMA --}}
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show shadow-sm">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ session('warning') }}

        <button type="button" class="close" data-dismiss="alert"
                aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}

        <button type="button" class="close" data-dismiss="alert"
                aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


{{-- FILTROS --}}
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i>
            Seleccionar local y semana
        </h3>
    </div>

    <div class="card-body">
            <form method="GET" action="{{ route('asignacion-turnos.consulta') }}"
                class="filtros-consulta">

                <div class="row align-items-end">

                    {{-- FILTRO POR LOCAL --}}
                    <div class="col-md-5 col-lg-5">
                        <div class="form-group mb-3">
                            <label for="sucursal_id">
                                <i class="fas fa-store mr-1"></i>
                                Local
                            </label>

                            <select name="sucursal_id"
                                    id="sucursal_id"
                                    class="form-control">
                                <option value="">Seleccione un local</option>

                                @foreach ($sucursales as $item)
                                    <option value="{{ $item->id }}"
                                        {{ (string) $sucursalId === (string) $item->id
                                            ? 'selected'
                                            : '' }}>
                                        {{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- FILTRO POR SEMANA --}}
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group mb-3">
                            <label for="inicio">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Semana desde
                            </label>

                            <input type="date"
                                name="inicio"
                                id="inicio"
                                class="form-control"
                                value="{{ $fechaInicio->format('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- BOTÓN CONSULTAR --}}
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group mb-3">
                            <button type="submit"
                                    class="btn btn-consultar-turnos btn-primary btn-block" >
                                <i class="fas fa-search mr-1"></i>
                                Consultar
                            </button>
                        </div>
                    </div>

                </div>
            </form>
    </div>
</div>


{{-- SUCURSALES CON PLANIFICACIÓN EN LA SEMANA --}}
<div class="card card-outline card-success shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-clipboard-check mr-2"></i>
            Sucursales con planificación registrada
        </h3>

        <div class="card-tools">
            <span class="badge badge-success">
                {{ $sucursalesPlanificadas->count() }} registradas
            </span>
        </div>
    </div>

    <div class="card-body">
        <p class="text-muted small">
            Semana del {{ $fechaInicio->format('d/m/Y') }}
            al {{ $fechaFin->format('d/m/Y') }}.
            Puedes abrir cualquier planificación registrada.
        </p>

        @forelse ($sucursalesPlanificadas as $sucursalPlanificada)
            <div class="d-flex justify-content-between align-items-center
                        flex-wrap border rounded p-3 mb-2">

                <div class="mr-3">
                    <div class="font-weight-bold text-dark">
                        <i class="fas fa-store text-success mr-2"></i>
                        {{ $sucursalPlanificada->nombre }}
                    </div>

                    <small class="text-muted">
                        Semana:
                        {{ $fechaInicio->format('d/m/Y') }}
                        al
                        {{ $fechaFin->format('d/m/Y') }}
                    </small>
                </div>

                <a href="{{ route('asignacion-turnos.consulta', [
                        'sucursal_id' => $sucursalPlanificada->id,
                        'inicio' => $fechaInicio->toDateString()
                    ]) }}"
                   class="btn btn-sm btn-outline-primary mt-2 mt-md-0">

                    <i class="fas fa-eye mr-1"></i>
                    Visualizar planificación
                </a>
            </div>
        @empty
            <div class="text-center text-muted py-3">
                <i class="far fa-calendar-times fa-2x mb-2"></i>

                <p class="mb-0">
                    Todavía no hay sucursales planificadas para esta semana.
                </p>
            </div>
        @endforelse
    </div>
</div>

@if ($sucursal)

    @php
        /*
         * Recopilar los horarios reales de la semana para la leyenda.
         */
        $horariosLeyenda = collect();

        foreach ($turnos as $grupoTurnos) {
            foreach ($grupoTurnos as $registro) {
                if ($registro->horario) {
                    $horariosLeyenda->push($registro->horario);
                }
            }
        }

        $horariosLeyenda = $horariosLeyenda->unique('id')->values();

        $totalTurnos = collect($turnos)->flatten(1)->count();

        $nombresDias = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo',
        ];
    @endphp

    {{-- ENCABEZADO DEL CALENDARIO --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap">

                <div>
                    <h3 class="card-title font-weight-bold mb-1">
                        <i class="fas fa-store text-primary mr-1"></i>
                        {{ $sucursal->nombre }}
                    </h3>

                    <div class="text-muted small mt-2">
                        <i class="far fa-calendar-alt mr-1"></i>
                        Semana del
                        <strong>{{ $fechaInicio->format('d/m/Y') }}</strong>
                        al
                        <strong>{{ $fechaFin->format('d/m/Y') }}</strong>
                    </div>
                </div>

                <div class="mt-2 mt-md-0">
                    <span class="badge badge-light border p-2">
                        <i class="fas fa-users text-primary mr-1"></i>
                        {{ $totalTurnos }} asignaciones
                    </span>
                </div>

            </div>
        </div>

        <div class="card-body calendar-body">

            {{-- CALENDARIO DE SIETE COLUMNAS --}}
            <div class="calendario-scroll">

                <div class="calendario-semanal">

                    {{-- ENCABEZADOS DE LOS DÍAS --}}
                    @foreach ($dias as $dia)
                        @php
                            $nombreDia = $nombresDias[$dia->format('l')] ?? $dia->format('l');
                            $esHoy = $dia->isToday();
                        @endphp

                        <div class="calendario-encabezado {{ $esHoy ? 'dia-actual' : '' }}">
                            <span>{{ $nombreDia }}</span>

                            @if ($esHoy)
                                <span class="indicador-hoy">HOY</span>
                            @endif
                        </div>
                    @endforeach

                    {{-- CONTENIDO DE CADA DÍA --}}
                    @foreach ($dias as $dia)

                        @php
                            $turnosDia = $turnos->get(
                                $dia->toDateString(),
                                collect()
                            );
                        @endphp

                        <div class="calendario-dia {{ $dia->isToday() ? 'dia-actual' : '' }}">

                            {{-- NÚMERO DEL DÍA, ARRIBA A LA IZQUIERDA --}}
                            <div class="numero-dia">
                                {{ $dia->format('d') }}
                            </div>

                            @if ($turnosDia->isNotEmpty())

                                <div class="lista-turnos-dia">

                                    @foreach ($turnosDia as $turno)

                                        @php
                                            $trabajador = $turno->trabajador;

                                            $nombreTrabajador = $trabajador
                                                ? trim(
                                                    $trabajador->primer_nombre . ' ' .
                                                    $trabajador->segundo_nombre . ' ' .
                                                    $trabajador->primer_apellido . ' ' .
                                                    $trabajador->segundo_apellido
                                                )
                                                : 'Trabajador no disponible';

                                            $cargoNombre = $turno->cargo->nombre
                                                ?? $trabajador?->cargo?->nombre
                                                ?? 'Cargo no especificado';

                                            $claseTurno = 'turno-sin-horario';
                                            $etiquetaTurno = 'Horario no disponible';
                                            $horaTexto = '';

                                            if ($turno->es_descanso) {
                                                $claseTurno = 'turno-descanso';
                                                $etiquetaTurno = 'DESCANSO';
                                            } elseif ($turno->horario) {

                                                $horario = $turno->horario;

                                                $horaInicio = \Carbon\Carbon::parse(
                                                    $horario->hora_ingreso);

                                                $horaSalida = \Carbon\Carbon::parse(
                                                    $horario->hora_salida);

                                                // Primer bloque horario.
                                                $horaTexto = $horaInicio->format('H:i')
                                                    . ' - '
                                                    . $horaSalida->format('H:i');

                                                // Segundo bloque para los horarios partidos.
                                                if (
                                                    strtoupper(trim($horario->tipo_turno ?? '')) === 'PARTIDO'
                                                    && $horario->hora_ingreso2
                                                    && $horario->hora_salida2
                                                ) {
                                                    $horaInicio2 = \Carbon\Carbon::parse(
                                                        $horario->hora_ingreso2
                                                    );

                                                    $horaSalida2 = \Carbon\Carbon::parse(
                                                        $horario->hora_salida2
                                                    );

                                                    $horaTexto .= ' / '
                                                        . $horaInicio2->format('H:i')
                                                        . ' - '
                                                        . $horaSalida2->format('H:i');
                                                }


                                                $etiquetaTurno = $horario->nombre;

                                                /*
                                                 * Colores automáticos según el horario:
                                                 * mañana, intermedio, tarde o jornada especial.
                                                 */
                                                $minutosInicio = (
                                                    $horaInicio->hour * 60
                                                ) + $horaInicio->minute;

                                                $minutosSalida = (
                                                    $horaSalida->hour * 60
                                                ) + $horaSalida->minute;

                                                $duracion = $minutosSalida - $minutosInicio;

                                                if ($duracion <= 0) {
                                                    $duracion += 1440;
                                                }

                                                if (
                                                    $duracion >= 600 &&
                                                    $horaInicio->hour >= 7 &&
                                                    $horaInicio->hour <= 9
                                                ) {
                                                    $claseTurno = 'turno-especial';
                                                } elseif ($minutosInicio >= 840) {
                                                    $claseTurno = 'turno-tarde';
                                                } elseif ($minutosInicio >= 600) {
                                                    $claseTurno = 'turno-intermedio';
                                                } else {
                                                    $claseTurno = 'turno-manana';
                                                }
                                            }
                                        @endphp

                                        <div class="tarjeta-turno {{ $claseTurno }}"
                                            title="{{ $nombreTrabajador }} | {{ $etiquetaTurno }}">

                                            <div class="tarjeta-turno-nombre">
                                                <i class="fas fa-user-circle mr-1"></i>
                                                {{ $nombreTrabajador }}
                                            </div>

                                            <div class="tarjeta-turno-cargo">
                                                {{ $cargoNombre }}
                                            </div>

                                            <div class="tarjeta-turno-horario">
                                                @if ($turno->es_descanso)
                                                    <i class="fas fa-bed mr-1"></i>
                                                    Día libre
                                                @elseif ($turno->horario)
                                                    <i class="far fa-clock mr-1"></i>
                                                    {{ $horaTexto }}
                                                @else
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    Sin horario
                                                @endif
                                            </div>

                                            @if ($turno->incidencias)
                                                <div class="tarjeta-turno-incidencia">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    {{ $turno->incidencias }}
                                                </div>
                                            @endif

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <div class="dia-sin-turnos">
                                    <i class="far fa-calendar-times"></i>
                                    <span>Sin asignaciones</span>
                                </div>

                            @endif

                        </div>

                    @endforeach

                </div>
            </div>

            {{-- BOTONES EXPORTAR --}}
                
            {{-- BARRA GENERAL DE EXPORTACIÓN Y COMPARTIR --}}
            <div class="acciones-planificacion mt-4 mb-4">
                <div class="acciones-planificacion-titulo">
                    <div>
                        <h5 class="mb-1 font-weight-bold">
                            <i class="fas fa-share-alt mr-2"></i>
                            Compartir planificación
                        </h5>
                        <p class="mb-0">
                            Descarga o comparte el calendario semanal completo.
                        </p>
                    </div>
                </div>

                <div class="acciones-planificacion-botones">
                    <a href="{{ route('asignacion-turnos.consulta.pdf', [
                            'sucursal_id' => $sucursal->id,
                            'inicio' => $fechaInicio->toDateString()
                        ]) }}"
                       class="btn btn-descargar-pdf">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Descargar PDF
                    </a>

                    <a href="{{ route('asignacion-turnos.consulta.excel', [
                            'sucursal_id' => $sucursal->id,
                            'inicio' => $fechaInicio->toDateString()
                        ]) }}"
                       class="btn btn-descargar-excel">
                        <i class="fas fa-file-excel mr-2"></i>
                        Descargar Excel
                    </a>

                    <button type="button"
                            class="btn btn-compartir-whatsapp"
                            data-toggle="modal"
                            data-target="#modalCompartirPlanificacion">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Compartir por WhatsApp
                    </button>
                </div>
            </div>

            {{-- MODAL PARA ELEGIR AL TRABAJADOR --}}
            @php
                $trabajadoresWhatsapp = $turnos
                    ->flatten(1)
                    ->filter(fn ($registro) => $registro->trabajador)
                    ->pluck('trabajador')
                    ->unique('id')
                    ->sortBy('primer_apellido')
                    ->values();
            @endphp

            <div class="modal fade"
                 id="modalCompartirPlanificacion"
                 tabindex="-1"
                 role="dialog"
                 aria-labelledby="tituloCompartirPlanificacion"
                 aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow">

                        <div class="modal-header modal-whatsapp-header">
                            <h5 class="modal-title" id="tituloCompartirPlanificacion">
                                <i class="fab fa-whatsapp mr-2"></i>
                                Compartir calendario semanal
                            </h5>

                            <button type="button"
                                    class="close text-white"
                                    data-dismiss="modal"
                                    aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                Primero descarga el PDF de la planificación.
                                Después selecciona al trabajador, abre WhatsApp
                                y adjunta ese archivo antes de enviarlo.
                            </div>

                            <p class="text-muted small">
                                Sucursal: <strong>{{ $sucursal->nombre }}</strong><br>
                                Semana:
                                <strong>
                                    {{ $fechaInicio->format('d/m/Y') }}
                                    al
                                    {{ $fechaFin->format('d/m/Y') }}
                                </strong>
                            </p>

                            @forelse ($trabajadoresWhatsapp as $trabajadorWhatsapp)
                                @php
                                    $telefono = preg_replace(
                                        '/\D+/',
                                        '',
                                        $trabajadorWhatsapp->telefono ?? ''
                                    );

                                    if (strlen($telefono) === 9) {
                                        $telefono = '51' . $telefono;
                                    }

                                    $nombreWhatsapp = trim(implode(' ', array_filter([
                                        $trabajadorWhatsapp->primer_nombre,
                                        $trabajadorWhatsapp->segundo_nombre,
                                        $trabajadorWhatsapp->primer_apellido,
                                        $trabajadorWhatsapp->segundo_apellido,
                                    ])));

                                    $mensajeWhatsapp =
                                        "Hola, {$trabajadorWhatsapp->primer_nombre}. " .
                                        "Te comparto la planificación semanal completa de " .
                                        "{$sucursal->nombre}, del " .
                                        $fechaInicio->format('d/m/Y') . ' al ' .
                                        $fechaFin->format('d/m/Y') .
                                        ". Por favor, revisa tus turnos asignados.";
                                @endphp

                                <div class="contacto-planificacion">
                                    <div class="contacto-planificacion-datos">
                                        <div class="contacto-planificacion-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>

                                        <div>
                                            <div class="font-weight-bold">
                                                {{ $nombreWhatsapp }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $telefono ?: 'Sin teléfono registrado' }}
                                            </small>
                                        </div>
                                    </div>

                                    @if ($telefono !== '')
                                        <a href="https://wa.me/{{ $telefono }}?text={{ rawurlencode($mensajeWhatsapp) }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="fab fa-whatsapp mr-1"></i>
                                            Abrir chat
                                        </a>
                                    @else
                                        <span class="badge badge-secondary">
                                            Sin teléfono
                                        </span>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    No hay trabajadores asignados en esta semana.
                                </div>
                            @endforelse
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-dismiss="modal">
                                Cerrar
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            {{-- LEYENDA DE TURNOS --}}
            <div class="leyenda-turnos mt-4">

                <div class="leyenda-titulo">
                    <i class="fas fa-palette text-primary mr-1"></i>
                    Leyenda de turnos y horarios
                </div>

                <div class="row mt-3">

                    {{-- TURNOS QUE EXISTEN EN LA SEMANA --}}
                    @forelse ($horariosLeyenda as $horarioLeyenda)

                        @php
                            $inicioLeyenda = \Carbon\Carbon::parse(
                                $horarioLeyenda->hora_ingreso
                            );

                            $salidaLeyenda = \Carbon\Carbon::parse(
                                $horarioLeyenda->hora_salida
                            );

                            $minutosInicioLeyenda = (
                                $inicioLeyenda->hour * 60
                            ) + $inicioLeyenda->minute;

                            $minutosSalidaLeyenda = (
                                $salidaLeyenda->hour * 60
                            ) + $salidaLeyenda->minute;

                            $duracionLeyenda = $minutosSalidaLeyenda
                                - $minutosInicioLeyenda;

                            if ($duracionLeyenda <= 0) {
                                $duracionLeyenda += 1440;
                            }

                            if (
                                $duracionLeyenda >= 600 &&
                                $inicioLeyenda->hour >= 7 &&
                                $inicioLeyenda->hour <= 9
                            ) {
                                $claseLeyenda = 'turno-especial';
                            } elseif ($minutosInicioLeyenda >= 840) {
                                $claseLeyenda = 'turno-tarde';
                            } elseif ($minutosInicioLeyenda >= 600) {
                                $claseLeyenda = 'turno-intermedio';
                            } else {
                                $claseLeyenda = 'turno-manana';
                            }
                        @endphp

                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                            <div class="elemento-leyenda">
                                <span class="muestra-color {{ $claseLeyenda }}"></span>

                                <div>
                                    <div class="elemento-leyenda-nombre">
                                        {{ $horarioLeyenda->nombre }}
                                    </div>

                                    <div class="elemento-leyenda-horas">
                                        {{ $inicioLeyenda->format('H:i') }}
                                        –
                                        {{ $salidaLeyenda->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty

                        <div class="col-12">
                            <p class="text-muted mb-2">
                                No hay horarios registrados en las asignaciones
                                de esta semana.
                            </p>
                        </div>

                    @endforelse

                    {{-- DESCANSO --}}
                    <div class="col-12 col-sm-6 col-lg-4 mb-3">
                        <div class="elemento-leyenda">
                            <span class="muestra-color turno-descanso"></span>
                            <div>
                                <div class="elemento-leyenda-nombre">
                                    Día de descanso
                                </div>
                                <div class="elemento-leyenda-horas">
                                    Sin jornada laboral
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="nota-leyenda">
                    <i class="fas fa-info-circle mr-1"></i>
                    Los colores identifican los turnos según sus horas de ingreso.
                    Las jornadas especiales se muestran en un color independiente.
                </div>

            </div>

        </div>
    </div>

@else

    <div class="card card-outline card-info">
        <div class="card-body text-center py-5">
            <i class="far fa-calendar-alt fa-3x text-info mb-3"></i>

            <h4>Consulta tu planificación semanal</h4>

            <p class="text-muted mb-0">
                Selecciona un local y una semana para visualizar
                los turnos establecidos en el calendario.
            </p>
        </div>
    </div>

@endif

@stop

@section('css')
<style>
    .calendar-body {
        padding: 20px;
    }

    .calendario-scroll {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 8px;
    }

    .calendario-semanal {
        display: grid;
        grid-template-columns: repeat(7, minmax(145px, 1fr));
        min-width: 1050px;
        border-top: 1px solid #dbe4ee;
        border-left: 1px solid #dbe4ee;
        border-radius: 10px;
        overflow: hidden;
        background: #ffffff;
    }

    .calendario-encabezado {
        min-height: 52px;
        padding: 13px 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #f1f5f9;
        color: #334155;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .35px;
        border-right: 1px solid #dbe4ee;
        border-bottom: 1px solid #dbe4ee;
    }

    .calendario-dia {
        min-width: 0;
        min-height: 225px;
        padding: 9px;
        position: relative;
        background: #ffffff;
        border-right: 1px solid #dbe4ee;
        border-bottom: 1px solid #dbe4ee;
    }

    .numero-dia {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        margin-bottom: 10px;
        border-radius: 9px;
        background: #f1f5f9;
        color: #334155;
        font-size: 15px;
        font-weight: 800;
    }

    .dia-actual {
        background-color: #eff8ff !important;
    }

    .calendario-encabezado.dia-actual {
        color: #075985;
    }

    .calendario-dia.dia-actual .numero-dia {
        color: #ffffff;
        background: #087f9f;
    }

    .indicador-hoy {
        padding: 3px 5px;
        border-radius: 5px;
        background: #dbeafe;
        color: #075985;
        font-size: 9px;
    }

    .lista-turnos-dia {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 9px;
    }

    .tarjeta-turno {
        width: 100%;
        padding: 10px 8px;
        border: 1px solid transparent;
        border-left-width: 4px;
        border-radius: 10px;
        overflow-wrap: anywhere;
        text-align: center;
        box-shadow: 0 2px 5px rgba(15, 23, 42, .06);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .tarjeta-turno:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(15, 23, 42, .10);
    }

    .tarjeta-turno-nombre {
        font-size: 12px;
        font-weight: 800;
        line-height: 1.45;
    }

    .tarjeta-turno-cargo {
        margin-top: 4px;
        font-size: 10px;
        line-height: 1.4;
        opacity: .85;
    }

    .tarjeta-turno-horario {
        margin-top: 7px;
        padding-top: 6px;
        border-top: 1px solid rgba(100, 116, 139, .20);
        font-size: 11px;
        font-weight: 700;
        white-space: normal;
    }

    .tarjeta-turno-incidencia {
        margin-top: 6px;
        padding: 5px;
        border-radius: 6px;
        background: rgba(255, 255, 255, .65);
        font-size: 10px;
        text-align: left;
    }

    /* Mañana: azul */
    .turno-manana {
        color: #1d4ed8;
        background: #dbeafe;
        border-color: #bfdbfe;
        border-left-color: #2563eb;
    }

    /* Intermedio: verde */
    .turno-intermedio {
        color: #166534;
        background: #dcfce7;
        border-color: #bbf7d0;
        border-left-color: #16a34a;
    }

    /* Tarde: naranja */
    .turno-tarde {
        color: #9a3412;
        background: #ffedd5;
        border-color: #fed7aa;
        border-left-color: #ea580c;
    }

    /* Jornada especial: morado */
    .turno-especial {
        color: #6b21a8;
        background: #f3e8ff;
        border-color: #e9d5ff;
        border-left-color: #9333ea;
    }

    /* Descanso: gris */
    .turno-descanso {
        color: #475569;
        background: #f1f5f9;
        border-color: #e2e8f0;
        border-left-color: #64748b;
    }

    .turno-sin-horario {
        color: #991b1b;
        background: #fee2e2;
        border-color: #fecaca;
        border-left-color: #dc2626;
    }

    .dia-sin-turnos {
        min-height: 110px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 9px;
        color: #94a3b8;
        font-size: 11px;
        text-align: center;
    }

    .dia-sin-turnos i {
        font-size: 23px;
    }

    /* Leyenda */
    .leyenda-turnos {
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .leyenda-titulo {
        color: #1e293b;
        font-size: 16px;
        font-weight: 800;
    }

    .elemento-leyenda {
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
        min-height: 64px;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
    }

    .muestra-color {
        flex: 0 0 13px;
        width: 13px;
        height: 40px;
        border-radius: 6px;
        border-left: 4px solid;
    }

    .muestra-color.turno-manana {
        border-left-color: #2563eb;
    }

    .muestra-color.turno-intermedio {
        border-left-color: #16a34a;
    }

    .muestra-color.turno-tarde {
        border-left-color: #ea580c;
    }

    .muestra-color.turno-especial {
        border-left-color: #9333ea;
    }

    .muestra-color.turno-descanso {
        border-left-color: #64748b;
    }

    .elemento-leyenda-nombre {
        color: #334155;
        font-size: 12px;
        font-weight: 800;
    }

    .elemento-leyenda-horas {
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
    }

    .nota-leyenda {
        margin-top: 10px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
    }

    @media (max-width: 767.98px) {
        .calendar-body {
            padding: 12px;
        }

        .leyenda-turnos {
            padding: 14px;
        }

        .calendario-semanal {
            grid-template-columns: repeat(7, minmax(145px, 1fr));
        }
    }


    /* Barra de acciones de la planificación */
    .acciones-planificacion {
        padding: 20px;
        border: 1px solid #dbe4ee;
        border-radius: 12px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 18px;
    }

    .acciones-planificacion-titulo h5 {
        color: #075985;
        font-size: 16px;
    }

    .acciones-planificacion-titulo p {
        color: #64748b;
        font-size: 12px;
    }

    .acciones-planificacion-botones {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .acciones-planificacion-botones .btn {
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(15, 23, 42, .08);
    }

    .btn-descargar-pdf {
        color: #fff;
        background: #b91c1c;
    }

    .btn-descargar-pdf:hover {
        color: #fff;
        background: #991b1b;
    }

    .btn-descargar-excel {
        color: #fff;
        background: #15803d;
    }

    .btn-descargar-excel:hover {
        color: #fff;
        background: #166534;
    }

    .btn-compartir-whatsapp {
        color: #fff;
        background: #16a34a;
    }

    .btn-compartir-whatsapp:hover {
        color: #fff;
        background: #15803d;
    }

    .modal-whatsapp-header {
        color: #fff;
        background: #087f9f;
    }

    .contacto-planificacion {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .contacto-planificacion:last-child {
        border-bottom: 0;
    }

    .contacto-planificacion-datos {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .contacto-planificacion-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        color: #075985;
        background: #e0f2fe;
        border-radius: 50%;
    }

    @media (max-width: 767.98px) {
        .acciones-planificacion {
            align-items: stretch;
            padding: 15px;
        }

        .acciones-planificacion-botones {
            flex-direction: column;
        }

        .acciones-planificacion-botones .btn {
            width: 100%;
        }

        .contacto-planificacion {
            align-items: flex-start;
        }
    }

    .filtros-consulta {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px 18px 4px;
    margin-bottom: 20px;
}

.filtros-consulta label {
    display: block;
    color: #334155;
    font-weight: 600;
    margin-bottom: 8px;
}

.filtros-consulta .form-control {
    height: 42px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
}

.btn-consultar-turnos {
    height: 42px;
    background: #087f9f;
    border-color: #087f9f;
    color: #fff;
    font-weight: 600;
    border-radius: 7px;
}

.btn-consultar-turnos:hover {
    background: #075985;
    border-color: #075985;
    color: #fff;
}

@media (max-width: 767.98px) {
    .filtros-consulta {
        padding: 14px 12px 2px;
    }
}

</style>
@stop
