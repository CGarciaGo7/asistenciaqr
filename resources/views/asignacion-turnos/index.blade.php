@extends('adminlte::page')

@section('title', 'Asignación de Turnos')

@section('content_header')
    <h1>
        <i class="fas fa-calendar-alt text-primary"></i>
        Asignación de Turnos
    </h1>
@stop

@section('content')

@php
    $nombresDias = [
        'Lunes', 'Martes', 'Miércoles', 'Jueves',
        'Viernes', 'Sábado', 'Domingo'
    ];
@endphp

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}

        <button type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>No se pudo guardar la planificación.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Filtros --}}
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i> Parámetros de consulta
        </h3>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('asignacion-turnos.index') }}">
            
            <div class="row align-items-end filtros-planificacion">

    {{-- LOCAL --}}
    <div class="col-md-5">
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

    {{-- FECHA DE INICIO --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="inicio">
                <i class="fas fa-calendar-alt mr-1"></i>
                Semana de planificación
            </label>

            {{-- Conserva aquí tu input actual y su valor --}}
            <input type="date"
                name="inicio"
                id="inicio"
                class="form-control"
                value="{{ $inicioSemana->format('Y-m-d') }}">
        </div>
    </div>

    {{-- ACCIONES --}}
    <div class="col-md-3">
        <div class="form-group mb-3">
            {{-- Conserva aquí el botón original --}}
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-search mr-1"></i>
                Consultar
            </button>
        </div>
    </div>

</div>
        </form>
    </div>
</div>

@if($sucursal)
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users"></i>
                Personal de {{ $sucursal->nombre }}
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary">
                    {{ $inicioSemana->format('d/m/Y') }}
                    al
                    {{ $finSemana->format('d/m/Y') }}
                </span>
            </div>
        </div>

        <form action="{{ route('asignacion-turnos.store') }}"
                method="POST"
                data-formulario-planificacion="1"
                data-santa-rosa2="{{ $esSantaRosa2 ? '1' : '0' }}">
                @csrf

            <input type="hidden" name="sucursal_id" value="{{ $sucursal->id }}">
            <input type="hidden" name="inicio_semana" value="{{ $inicioSemana->toDateString() }}">

            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Selecciona un horario para cada día trabajado
                    y marca exactamente un día como descanso por trabajador.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-primary">
                            <tr>
                                <th style="min-width: 220px;">
                                    Trabajador
                                </th>

                                @foreach($dias as $dia)
                                    <th style="min-width: 175px;">
                                        {{ $nombresDias[$dia->dayOfWeekIso - 1] }}
                                        <br>
                                        {{ $dia->format('d/m') }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($trabajadores as $trabajador)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $trabajador->primer_nombre }}
                                            {{ $trabajador->segundo_nombre }}
                                            {{ $trabajador->primer_apellido }}
                                            {{ $trabajador->segundo_apellido }}
                                        </strong>

                                        <br>
                                        <small class="text-muted">
                                            {{ $trabajador->cargo->nombre
                                                ?? 'Sin cargo' }}
                                        </small>
                                    </td>

                                    @foreach($dias as $dia)
                                        @php
                                            $fechaDia = $dia->toDateString();

                                            // Al consultar, todos los campos comienzan vacíos.
                                            // Los turnos y descansos se completarán al generar la propuesta.
                                            $valorActual = '';
                                        @endphp

                                        <td class="celda-turno">
                                            <div class="etiqueta-descanso d-none">
                                                <i class="fas fa-bed"></i> DÍA DE DESCANSO
                                            </div>
                                            <select
                                                name="asignaciones[{{ $trabajador->id }}][{{ $fechaDia }}]"
                                                class="form-control form-control-sm turno-select"
                                                required>

                                                <option value="">
                                                    Seleccionar
                                                </option>

                                                <option value="descanso">
                                                        Descanso
                                                </option>

                                                @foreach($horarios as $horario)
                                                        <option
                                                        value="{{ $horario->id }}"
                                                        data-hora-ingreso="{{ substr((string) $horario->hora_ingreso, 0, 5) }}"
                                                        data-hora-salida="{{ substr((string) $horario->hora_salida, 0, 5) }}"
                                                        data-hora-ingreso2="{{ substr((string) $horario->hora_ingreso2, 0, 5) }}"
                                                        data-hora-salida2="{{ substr((string) $horario->hora_salida2, 0, 5) }}"
                                                        data-tipo-turno="{{ $horario->tipo_turno }}"
                                                        data-especial="{{ substr((string) $horario->hora_ingreso, 0, 5) === '08:00' && substr((string) $horario->hora_salida, 0, 5) === '20:00' ? '1' : '0' }}" >
                                                        {{ $horario->nombre }}
                                                        ({{ substr((string) $horario->hora_ingreso, 0, 5) }}–{{ substr((string) $horario->hora_salida, 0, 5) }})
                                                    </option>
                                                @endforeach
                                            </select>

                                            <div class="horario-turno mt-1">
                                                <small class="hora-turno text-info"></small>
                                            </div>
                                          
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8"
                                        class="text-center text-muted">
                                        No hay trabajadores activos
                                        registrados en esta sucursal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($trabajadores->isNotEmpty())
                
                <div class="card-footer d-flex justify-content-between flex-wrap">
                    <button type="button"
                            id="btnGenerarPropuesta"
                            class="btn btn-primary mb-2">
                        <i class="fas fa-random"></i>
                        Generar propuesta aleatoria
                    </button>

                    <button type="submit" class="btn btn-success mb-2">
                        <i class="fas fa-save"></i>
                        Guardar planificación semanal
                    </button>
                </div>

            @endif
        </form>
    </div>

@else
    <div class="alert alert-secondary">
        <i class="fas fa-info-circle"></i>
        Selecciona una sucursal para consultar y planificar sus turnos.
    </div>
@endif

@stop

@section('css')
<style>
    .celda-turno {
        min-width: 175px;
        vertical-align: middle !important;
        transition: background-color 0.2s ease;
    }

    .celda-turno.dia-descanso {
        background-color: #fff3cd !important;
        border: 2px solid #f0ad4e !important;
    }

    .celda-turno.dia-descanso .turno-select {
        background-color: #fff8e1;
        border-color: #e0a800;
        color: #856404;
        font-weight: 700;
    }

    .etiqueta-descanso {
        background-color: #ffc107;
        color: #533f03;
        border-radius: 4px;
        padding: 5px 7px;
        margin-bottom: 7px;
        text-align: center;
        font-size: 11px;
        font-weight: bold;
    }

    .celda-turno.dia-descanso input {
        border-color: #e0a800;
    }
</style>

<style>

/* Turno de mañana */
.hora-turno.hora-manana {
    color: #087f9f !important;
    background-color: #e0f2fe;
    border-left: 4px solid #0891b2;
    padding: 4px 6px;
    border-radius: 4px;
}

/* Turno intermedio partido */
.hora-turno.hora-intermedio {
    color: #7c3aed !important;
    background-color: #ede9fe;
    border-left: 4px solid #8b5cf6;
    padding: 4px 6px;
    border-radius: 4px;
}

/* Turno de tarde */
.hora-turno.hora-tarde {
    color: #b45309 !important;
    background-color: #fef3c7;
    border-left: 4px solid #f59e0b;
    padding: 4px 6px;
    border-radius: 4px;
}

/* Turno especial de 08:00 a 20:00 */
.hora-turno.hora-especial {
    color: #166534 !important;
    background-color: #dcfce7;
    border-left: 4px solid #22c55e;
    padding: 4px 6px;
    border-radius: 4px;
}

/* Día de descanso */
.hora-turno.hora-descanso {
    color: #92400e !important;
    background-color: #fef3c7;
    border-left: 4px solid #f59e0b;
    padding: 4px 6px;
    border-radius: 4px;
}

/* Selector sin turno */
.hora-turno.hora-vacia {
    color: #64748b !important;
    font-size: 12px;
}
```


}
</style>
@stop


@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.querySelector(
        'form[data-formulario-planificacion="1"]'
    );

    const boton = document.getElementById('btnGenerarPropuesta');

    if (!formulario || !boton) {
        console.error(
            'No se encontró el formulario o el botón de propuesta.'
        );
        return;
    }

    function actualizarHoraTurno(select) {
    const celda = select.closest('.celda-turno') || select.parentElement;
    const etiqueta = celda.querySelector('.hora-turno');
    const opcion = select.options[select.selectedIndex];

    if (!etiqueta) return;

    // Reiniciar los colores anteriores.
    etiqueta.classList.remove(
        'hora-manana',
        'hora-intermedio',
        'hora-tarde',
        'hora-especial',
        'hora-descanso',
        'hora-vacia'
    );

    if (!opcion || !opcion.value) {
        etiqueta.textContent = '';
        etiqueta.classList.add('hora-vacia');
        return;
    }

    if (opcion.value === 'descanso') {
        etiqueta.textContent = '';
        etiqueta.classList.add('hora-descanso');
        return;
    }

    const entrada = opcion.dataset.horaIngreso;
    const salida = opcion.dataset.horaSalida;
    const entrada2 = opcion.dataset.horaIngreso2;
    const salida2 = opcion.dataset.horaSalida2;
    const tipo = (opcion.dataset.tipoTurno || '').toUpperCase();

    if (!entrada || !salida) {
        etiqueta.textContent = opcion.textContent.trim();
        etiqueta.classList.add('hora-vacia');
        return;
    }

    // Turno especial: 08:00 a 20:00.
    if (
        opcion.dataset.especial === '1' ||
        (tipo === 'CONTINUO' && entrada === '08:00' && salida === '20:00')
    ) {
        etiqueta.textContent = `${entrada} - ${salida}`;
        etiqueta.classList.add('hora-especial');
        return;
    }

    // Turno de mañana.
    if (tipo === 'CONTINUO' && entrada === '07:00' && salida === '15:00') {
        etiqueta.textContent = `${entrada} - ${salida}`;
        etiqueta.classList.add('hora-manana');
        return;
    }

    // Turno intermedio partido.
    if (
        tipo === 'PARTIDO' &&
        entrada === '10:00' &&
        salida === '14:00' &&
        entrada2 === '17:00' &&
        salida2 === '21:00'
    ) {
        etiqueta.textContent =
            `${entrada} - ${salida} | ${entrada2} - ${salida2}`;
        etiqueta.classList.add('hora-intermedio');
        return;
    }

    // Turno de tarde.
    if (
        tipo === 'CONTINUO' &&
        ['14:00', '15:00'].includes(entrada) &&
        ['22:00', '23:00'].includes(salida)
    ) {
        etiqueta.textContent = `${entrada} - ${salida}`;
        etiqueta.classList.add('hora-tarde');
        return;
    }

    // Otros horarios disponibles.
    etiqueta.textContent =
        tipo === 'PARTIDO' && entrada2 && salida2
            ? `${entrada} - ${salida} | ${entrada2} - ${salida2}`
            : `${entrada} - ${salida}`;

    etiqueta.classList.add('hora-vacia');
    }

    const esSantaRosa2 = formulario.dataset.santaRosa2 === '1';

    // Obtener únicamente las filas con los siete días de la semana.
    function obtenerFilas() {
        return Array.from(
            formulario.querySelectorAll('tbody tr')
        ).filter(fila =>
            fila.querySelectorAll('.turno-select').length === 7
        );
    }

    function mezclar(array) {
        const resultado = [...array];

        for (let i = resultado.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [resultado[i], resultado[j]] = [
                resultado[j],
                resultado[i]
            ];
        }

        return resultado;
    }

    function actualizarDescanso(select) {
        const celda = select.closest('.celda-turno');

        if (!celda) return;

        const descanso = select.value === 'descanso';

        celda.classList.toggle('dia-descanso', descanso);

        const etiqueta = celda.querySelector('.etiqueta-descanso');

        if (etiqueta) {
            etiqueta.classList.toggle('d-none', !descanso);
        }
    }

    function esEspecial(opcion) {
        return opcion?.dataset.especial === '1'
            || (
                opcion?.dataset.horaIngreso === '08:00'
                && opcion?.dataset.horaSalida === '20:00'
                && (opcion?.dataset.tipoTurno || '').toUpperCase()
                    === 'CONTINUO'
            );
    }

    // Clasificar el horario sin modificar la interfaz.
    function tipoHorario(opcion) {
        if (
            !opcion?.value ||
            opcion.value === 'descanso' ||
            esEspecial(opcion)
        ) {
            return null;
        }

        const entrada = opcion.dataset.horaIngreso;
        const salida = opcion.dataset.horaSalida;
        const entrada2 = opcion.dataset.horaIngreso2;
        const salida2 = opcion.dataset.horaSalida2;
        const tipoTurno = (
            opcion.dataset.tipoTurno || ''
        ).toUpperCase();

        if (!entrada || !salida) {
            return null;
        }

        // Mañana: 07:00 a 15:00.
        if (
            tipoTurno === 'CONTINUO' &&
            entrada === '07:00' &&
            salida === '15:00'
        ) {
            return 'manana';
        }

        // Intermedio partido:
        // 10:00-14:00 y 17:00-21:00.
        if (
            tipoTurno === 'PARTIDO' &&
            entrada === '10:00' &&
            salida === '14:00' &&
            entrada2 === '17:00' &&
            salida2 === '21:00'
        ) {
            return 'intermedio';
        }

        // Turno de tarde: el horario disponible debe terminar
        // a las 22:00 o 23:00.
        if (
            tipoTurno === 'CONTINUO' &&
            ['14:00', '15:00'].includes(entrada) &&
            ['22:00', '23:00'].includes(salida)
        ) {
            return 'tarde';
        }

        return null;
    }

    function opcionesPorTipo(select, tipo) {
        return Array.from(select.options).filter(
            opcion => tipoHorario(opcion) === tipo
        );
    }

    function elegir(select, tipo) {
        const opciones = opcionesPorTipo(select, tipo);

        if (!opciones.length) {
            return null;
        }

        return mezclar(opciones)[0].value;
    }

    function elegirEspecial(select) {
        const opcion = Array.from(select.options).find(esEspecial);

        return opcion?.value ?? null;
    }

    function asignar(select, valor) {
        select.value = valor ?? '';

        actualizarDescanso(select);

        // Actualizar la hora seleccionada si la función está disponible.
        if (typeof actualizarHoraTurno === 'function') {
            actualizarHoraTurno(select);
        }

        // Notificar los cambios a los demás listeners.
        select.dispatchEvent(
            new Event('change', { bubbles: true })
        );
    }

    // Actualizar descanso y horas al cargar y al seleccionar manualmente.
   // Inicializar selectores vacíos al consultar.
// Los colores y etiquetas de descanso solo se activan
// cuando el usuario selecciona una opción o se genera la propuesta.
formulario.querySelectorAll('.turno-select').forEach(select => {
    actualizarHoraTurno(select);

    select.addEventListener('change', function () {
        actualizarDescanso(this);
        actualizarHoraTurno(this);
    });
});

    boton.addEventListener('click', async function (evento) {
        evento.preventDefault();

        const filas = obtenerFilas();
        const total = filas.length;

        if (total < 2 || total > 5) {
            await Swal.fire(
                'Cantidad de técnicos no válida',
                'La propuesta requiere entre 2 y 5 técnicos vendedores activos.',
                'warning'
            );
            return;
        }

        if (esSantaRosa2 && total !== 2) {
            await Swal.fire(
                'Santa Rosa 2',
                'Esta sucursal debe tener exactamente dos técnicas activas.',
                'warning'
            );
            return;
        }

        if (!esSantaRosa2 && total === 2) {
            await Swal.fire(
                'Regla pendiente',
                'La excepción para establecimientos con dos técnicos está reservada a Santa Rosa 2.',
                'warning'
            );
            return;
        }

        const confirmacion = await Swal.fire({
            icon: 'question',
            title: '¿Generar propuesta aleatoria?',
            text: 'Se reemplazarán las selecciones actuales. La propuesta no se guardará hasta confirmar la planificación.',
            showCancelButton: true,
            confirmButtonText: 'Generar propuesta',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmacion.isConfirmed) {
            return;
        }

        // Cada técnico tendrá un día de descanso diferente.
        const diasDescanso = mezclar([0, 1, 2, 3, 4, 5, 6])
            .slice(0, total);

        // Construir la planificación de los siete días.
        for (let dia = 0; dia < 7; dia++) {
            const descansan = filas
                .map((fila, indice) =>
                    diasDescanso[indice] === dia ? indice : -1
                )
                .filter(indice => indice !== -1);

            const indicesActivos = filas
                .map((fila, indice) => indice)
                .filter(indice => !descansan.includes(indice));

            const roles = [];

            if (esSantaRosa2) {
                if (descansan.length === 1) {
                    roles.push('especial');
                } else {
                    roles.push('manana', 'tarde');
                }
            } else if (total === 5) {
                if (descansan.length === 1) {
                    roles.push(
                        'manana', 'manana', 'tarde', 'tarde'
                    );
                } else {
                    roles.push(
                        'manana', 'manana', 'intermedio',
                        'tarde', 'tarde'
                    );
                }
            } else if (total === 4) {
                if (descansan.length === 1) {
                    roles.push('manana', 'tarde', 'tarde');
                } else {
                    roles.push(
                        'manana', 'manana', 'tarde', 'tarde'
                    );
                }
            } else if (total === 3) {
                if (descansan.length === 1) {
                    roles.push('manana', 'tarde');
                } else {
                    roles.push('manana', 'intermedio', 'tarde');
                }
            }

            // Comprobar que haya un rol para cada técnico activo.
            if (roles.length !== indicesActivos.length) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Distribución incompatible',
                    text: 'La cantidad de turnos no coincide con los técnicos activos del día ' +
                        (dia + 1) + '.'
                });
                return;
            }

            const rolesMezclados = mezclar(roles);
            const trabajadoresMezclados = mezclar(indicesActivos);
            const asignacionesDia = [];

            for (
                let i = 0;
                i < trabajadoresMezclados.length;
                i++
            ) {
                const indiceTrabajador = trabajadoresMezclados[i];

                const select = filas[indiceTrabajador]
                    .querySelectorAll('.turno-select')[dia];

                const rol = rolesMezclados[i];

                const valor = rol === 'especial'
                    ? elegirEspecial(select)
                    : elegir(select, rol);

                if (!valor) {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Faltan horarios compatibles',
                        text: 'No se encontró un horario disponible para cubrir todos los turnos del día ' +
                            (dia + 1) +
                            '. Revisa los horarios activos del establecimiento.'
                    });
                    return;
                }

                asignacionesDia.push({ select, valor });
            }

            // Aplicar las asignaciones después de validar el día.
            asignacionesDia.forEach(item => {
                asignar(item.select, item.valor);
            });

            // Aplicar el descanso de los técnicos correspondientes.
            descansan.forEach(indice => {
                const select = filas[indice]
                    .querySelectorAll('.turno-select')[dia];

                asignar(select, 'descanso');
            });
        }
        

        await Swal.fire({
            icon: 'success',
            title: 'Propuesta generada',
            text: 'Se generó la propuesta. Revisa los horarios y los descansos antes de guardar la planificación.'
        });
    });
});
</script>


@stop
