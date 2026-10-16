<?php

namespace App\Http\Controllers;

use App\Models\AsignacionTurno;
use App\Models\Horario;
use App\Models\Sucursal;
use App\Models\Trabajador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlanificacionSemanalExport;
use App\Models\PlanificacionSemanal;


class AsignacionTurnoController extends Controller
{
    
public function index(Request $request)
{
    $fecha = $request->input('inicio', now()->toDateString());

    $inicioSemana = Carbon::parse($fecha)->startOfWeek(Carbon::MONDAY);
    $finSemana = $inicioSemana->copy()->endOfWeek(Carbon::SUNDAY);

    $sucursales = Sucursal::orderBy('nombre')->get();

    $sucursalId = $request->input('sucursal_id');
    $sucursal = $sucursalId
        ? Sucursal::find($sucursalId)
        : null;

    $trabajadores = collect();
    $horarios = collect();
    $asignaciones = collect();

    $esSantaRosa2 = $sucursal !== null
        ? $this->esSantaRosa2($sucursal)
        : false;

    if ($sucursal) {
       
        $trabajadores = Trabajador::with('cargo')
            ->where('sucursal_id', $sucursal->id)
            ->where('estado', 1)
            ->whereHas('cargo', function ($query) {
                $query->whereIn(
                    \Illuminate\Support\Facades\DB::raw('UPPER(TRIM(nombre))'),
                    [
                        'TECNICO VENDEDOR',
                        'ENCARGADO DE LOCAL',
                    ]
                );
            })
            ->orderBy('primer_apellido')
            ->orderBy('primer_nombre')
            ->get();

        // El método auxiliar ya devuelve una colección filtrada.
        $horarios = $this->horariosDisponiblesParaSucursal($sucursal);

        // Santa Rosa 2 no admite turnos partidos.
        if ($esSantaRosa2) {
            $horarios = $horarios->filter(
                fn ($horario) => $horario->tipo_turno !== 'PARTIDO'
            );
        }

        // Ordenar los horarios por nombre.
        $horarios = $horarios->sortBy('nombre')->values();

        $asignaciones = AsignacionTurno::where('sucursal_id', $sucursal->id)
            ->whereBetween('fecha', [
                $inicioSemana->toDateString(),
                $finSemana->toDateString(),
            ])
            ->get()
            ->keyBy(fn ($asignacion) =>
                $asignacion->trabajador_id . '_' .
                $asignacion->fecha->toDateString()
            );
    }

    $dias = collect();

    for (
        $dia = $inicioSemana->copy();
        $dia->lte($finSemana);
        $dia->addDay()
    ) {
        $dias->push($dia->copy());
    }

    return view('asignacion-turnos.index', compact(
        'sucursales',
        'sucursal',
        'sucursalId',
        'trabajadores',
        'horarios',
        'asignaciones',
        'inicioSemana',
        'finSemana',
        'dias',
        'esSantaRosa2'
    ));
}


public function store(Request $request)
{
    $request->validate([
        'sucursal_id' => ['required', 'integer', 'exists:sucursals,id'],
        'inicio_semana' => ['required', 'date_format:Y-m-d'],
        'asignaciones' => ['required', 'array'],
        'incidencias' => ['nullable', 'array'],
    ]);

    $inicioSemana = Carbon::parse($request->inicio_semana)->startOfDay();

    if ($inicioSemana->dayOfWeekIso !== 1) {
        throw ValidationException::withMessages([
            'inicio_semana' => 'La fecha inicial debe corresponder a un lunes.',
        ]);
    }


$finSemana = $inicioSemana->copy()->addDays(6);

$sucursal = Sucursal::findOrFail($request->sucursal_id);

// Comprobar si ya existe una planificación registrada para esa semana.
$planificacionExiste = PlanificacionSemanal::where(
    'sucursal_id',
    $sucursal->id
)
    ->whereDate('fecha_inicio', $inicioSemana->toDateString())
    ->where('estado', 'registrada')
    ->exists();

// También reconocer las asignaciones antiguas que todavía no tengan
// su registro en la tabla planificaciones_semanales.
$asignacionesExistentes = AsignacionTurno::where(
    'sucursal_id',
    $sucursal->id
)
    ->whereBetween('fecha', [
        $inicioSemana->toDateString(),
        $finSemana->toDateString(),
    ])
    ->exists();

    if ($planificacionExiste || $asignacionesExistentes) {
        return redirect()
            ->route('asignacion-turnos.consulta', [
                'sucursal_id' => $sucursal->id,
                'inicio' => $inicioSemana->toDateString(),
            ])
            ->with(
                'warning',
                'Esta sucursal ya tiene una planificación para la semana del ' .
                $inicioSemana->format('d/m/Y') . ' al ' .
                $finSemana->format('d/m/Y') .
                '. No se puede registrar otra planificación para la misma semana.'
            );
    }

    $esSantaRosa2 = $this->esSantaRosa2($sucursal);

    $trabajadores = Trabajador::with('cargo')
        ->where('sucursal_id', $sucursal->id)
        ->where('estado', 1)
        ->whereHas('cargo', function ($query) {
    $query->whereIn(
        DB::raw('UPPER(TRIM(nombre))'),
        [
            'TECNICO VENDEDOR',
            'ENCARGADO DE LOCAL',
        ]
    );
})
        ->orderBy('primer_apellido')
        ->orderBy('primer_nombre')
        ->get();

    $cantidadTrabajadores = $trabajadores->count();

    if ($cantidadTrabajadores < 2 || $cantidadTrabajadores > 5) {
        throw ValidationException::withMessages([
            'asignaciones' =>
                'La planificación requiere entre 2 y 5 técnicos vendedores activos.',
        ]);
    }

    if ($esSantaRosa2 && $cantidadTrabajadores !== 2) {
        throw ValidationException::withMessages([
            'asignaciones' =>
                'Santa Rosa 2 debe tener exactamente dos técnicas activas.',
        ]);
    }

    $horariosValidos = $this->horariosDisponiblesParaSucursal($sucursal)
        ->keyBy('id');

    if ($horariosValidos->isEmpty()) {
        throw ValidationException::withMessages([
            'asignaciones' =>
                'No hay horarios activos disponibles para este establecimiento.',
        ]);
    }

    $horarioEspecial = $horariosValidos->first(
        fn ($horario) => $this->esHorarioEspecial($horario)
    );

    if ($esSantaRosa2 && !$horarioEspecial) {
        throw ValidationException::withMessages([
            'asignaciones' =>
                'Falta registrar el horario especial de 08:00 a 20:00 para Santa Rosa 2.',
        ]);
    }

    $seleccionesPorTrabajador = [];
    $descansoPorFecha = [];

    // Cada técnico debe descansar exactamente un día.
    foreach ($trabajadores as $trabajador) {
        $selecciones = $request->input(
            "asignaciones.{$trabajador->id}",
            []
        );

        $cantidadDescansos = 0;

        for ($i = 0; $i < 7; $i++) {
            $fecha = $inicioSemana->copy()->addDays($i)->toDateString();
            $seleccion = $selecciones[$fecha] ?? null;

            if ($seleccion === 'descanso') {
                $cantidadDescansos++;

                if (isset($descansoPorFecha[$fecha])) {
                    throw ValidationException::withMessages([
                        'asignaciones' =>
                            'Solo puede descansar un técnico por día. Revisa el ' .
                            Carbon::parse($fecha)->format('d/m/Y') . '.',
                    ]);
                }

                $descansoPorFecha[$fecha] = $trabajador->id;
                continue;
            }

            if (
                !is_numeric($seleccion)
                || !$horariosValidos->has((int) $seleccion)
            ) {
                throw ValidationException::withMessages([
                    "asignaciones.{$trabajador->id}" =>
                        "Selecciona un horario válido o descanso para {$trabajador->primer_nombre} {$trabajador->primer_apellido}.",
                ]);
            }
        }

        if ($cantidadDescansos !== 1) {
            throw ValidationException::withMessages([
                "asignaciones.{$trabajador->id}" =>
                    "Cada técnico debe tener exactamente un día de descanso: {$trabajador->primer_nombre} {$trabajador->primer_apellido}.",
            ]);
        }

        $seleccionesPorTrabajador[$trabajador->id] = $selecciones;
    }

    // Comprobar la distribución de los turnos de cada día.
    for ($i = 0; $i < 7; $i++) {
        $fecha = $inicioSemana->copy()->addDays($i)->toDateString();

        $activosDelDia = [];
        $descansa = $descansoPorFecha[$fecha] ?? null;

        foreach ($trabajadores as $trabajador) {
            if ($trabajador->id === $descansa) {
                continue;
            }

            $seleccion = $seleccionesPorTrabajador[$trabajador->id][$fecha];

            $activosDelDia[] = $horariosValidos->get((int) $seleccion);
        }

        // Regla exclusiva de Santa Rosa 2.
        if ($esSantaRosa2) {
            foreach ($activosDelDia as $horario) {
                if ($horario->tipo_turno === 'PARTIDO') {
                    throw ValidationException::withMessages([
                        'asignaciones' =>
                            'Santa Rosa 2 no permite turnos partidos.',
                    ]);
                }

                if (
                    $descansa !== null
                    && !$this->esHorarioEspecial($horario)
                ) {
                    throw ValidationException::withMessages([
                        'asignaciones' =>
                            'Cuando una técnica descansa en Santa Rosa 2, la otra debe trabajar de 08:00 a 20:00.',
                    ]);
                }

                if (
                    $descansa === null
                    && $this->esHorarioEspecial($horario)
                ) {
                    throw ValidationException::withMessages([
                        'asignaciones' =>
                            'El turno de 08:00 a 20:00 solo se utiliza cuando la otra técnica descansa.',
                    ]);
                }
            }

            if ($descansa === null) {
                $tipos = array_map(
                    fn ($horario) => $this->clasificarHorario(
                        $horario,
                        $sucursal
                    ),
                    $activosDelDia
                );

                if (in_array('invalido', $tipos, true)) {
                    throw ValidationException::withMessages([
                        'asignaciones' =>
                            'Uno de los horarios de Santa Rosa 2 no se reconoce como mañana o tarde. Revisa las horas configuradas en el módulo Horarios.',
                    ]);
                }

                sort($tipos);

                $esperados = ['manana', 'tarde'];
                sort($esperados);

                if ($tipos !== $esperados) {
                    throw ValidationException::withMessages([
                        'asignaciones' =>
                            'En Santa Rosa 2, los días normales deben tener un turno de mañana y otro de tarde.',
                    ]);
                }
            }

            continue;
        }

        // Clasificar los horarios de los trabajadores activos.
        $tiposClasificados = array_map(
            fn ($horario) => $this->clasificarHorario(
                $horario,
                $sucursal
            ),
            $activosDelDia
        );

        // Comprobar que todos los horarios sean reconocidos.
        if (in_array('invalido', $tiposClasificados, true)) {
            throw ValidationException::withMessages([
                'asignaciones' =>
                    'Hay un horario que no coincide con las reglas de la sucursal para el '
                    . Carbon::parse($fecha)->format('d/m/Y')
                    . '. Revisa las horas de entrada y salida configuradas.',
            ]);
        }

        // Contar cuántos trabajadores tienen cada tipo de turno.
        $tipos = array_count_values($tiposClasificados);

        $esperado = $this->distribucionEsperada(
            $cantidadTrabajadores,
            $descansa !== null
        );

        // Ordenar las claves antes de comparar las distribuciones.
        ksort($tipos);
        ksort($esperado);

        if ($tipos != $esperado) {
            throw ValidationException::withMessages([
                'asignaciones' =>
                    'La distribución de turnos no cumple las reglas del establecimiento para el '
                    . Carbon::parse($fecha)->format('d/m/Y')
                    . '. Distribución detectada: '
                    . json_encode($tipos, JSON_UNESCAPED_UNICODE)
                    . '. Distribución esperada: '
                    . json_encode($esperado, JSON_UNESCAPED_UNICODE)
                    . '. Revisa los turnos de mañana, intermedio y tarde.',
            ]);
        }

        // Verificar apertura y cierre efectivos.
        $horaApertura = collect($activosDelDia)
            ->map(fn ($horario) => substr((string) $horario->hora_ingreso, 0, 5))
            ->min();

        $horaCierre = collect($activosDelDia)
            ->map(fn ($horario) => substr((string) $horario->hora_salida, 0, 5))
            ->max();

        $cierreRequerido = $this->horaCierreSucursal($sucursal);

        if ($horaApertura > '07:00' || $horaCierre < $cierreRequerido) {
            throw ValidationException::withMessages([
                'asignaciones' =>
                    'La cobertura del ' . Carbon::parse($fecha)->format('d/m/Y') .
                    ' debe comenzar a las 07:00 y finalizar a las ' .
                    $cierreRequerido . '.',
            ]);
        }
    }

    // Preparar registros para guardar.
    $datosGuardar = [];

    foreach ($trabajadores as $trabajador) {
        $selecciones = $seleccionesPorTrabajador[$trabajador->id];

        for ($i = 0; $i < 7; $i++) {
            $fecha = $inicioSemana->copy()->addDays($i);
            $fechaTexto = $fecha->toDateString();

            $seleccion = $selecciones[$fechaTexto];
            $esDescanso = $seleccion === 'descanso';

            $datosGuardar[] = [
                'trabajador_id' => $trabajador->id,
                'sucursal_id' => $sucursal->id,
                'cargo_id' => $trabajador->cargo_id,
                'horario_id' => $esDescanso ? null : (int) $seleccion,
                'fecha' => $fechaTexto,
                'numero_semana' => $fecha->isoWeek(),
                'anio' => $fecha->isoWeekYear(),
                'es_descanso' => $esDescanso,
                'incidencias' => data_get(
                    $request->input('incidencias', []),
                    "{$trabajador->id}.{$fechaTexto}"
                ),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }

    
DB::transaction(function () use (
    $datosGuardar,
    $sucursal,
    $inicioSemana,
    $finSemana
) {
    // Registrar la existencia de la planificación semanal.
    PlanificacionSemanal::create([
        'sucursal_id' => $sucursal->id,
        'fecha_inicio' => $inicioSemana->toDateString(),
        'fecha_fin' => $finSemana->toDateString(),
        'estado' => 'registrada',
    ]);

    // Guardar las asignaciones de los trabajadores.
    foreach ($datosGuardar as $datos) {
        AsignacionTurno::updateOrCreate(
            [
                'trabajador_id' => $datos['trabajador_id'],
                'fecha' => $datos['fecha'],
            ],
            $datos
        );
    }
});

    return redirect()
        ->route('asignacion-turnos.consulta', [
            'sucursal_id' => $sucursal->id,
            'inicio' => $inicioSemana->toDateString(),
        ])
        ->with('success', 'La planificación semanal se guardó correctamente.');
}


/** Distribución requerida por cantidad de técnicos.*/

private function distribucionEsperada(int $total, bool $hayDescanso): array
{
    if ($total === 5) {
        return $hayDescanso
            ? ['manana' => 2, 'tarde' => 2]
            : ['manana' => 2, 'intermedio' => 1, 'tarde' => 2];
    }
    
    if ($total === 4) {
        return $hayDescanso
            ? [
                'manana' => 1,
                'intermedio' => 1,
                'tarde' => 1,
            ]
            : [
                'manana' => 2,
                'tarde' => 2,
            ];
    }

    if ($total === 3) {
        return $hayDescanso
            ? ['manana' => 1, 'tarde' => 1]
            : ['manana' => 1, 'intermedio' => 1, 'tarde' => 1];
    }

    return [];
}

/** Clasifica un horario por sus horas reales.*/

private function clasificarHorario(Horario $horario, Sucursal $sucursal): string
{
    $entrada = substr((string) $horario->hora_ingreso, 0, 5);
    $salida = substr((string) $horario->hora_salida, 0, 5);
    $entrada2 = substr((string) $horario->hora_ingreso2, 0, 5);
    $salida2 = substr((string) $horario->hora_salida2, 0, 5);

    // Turno de mañana: 07:00 a 15:00.
    if (
        $horario->tipo_turno === 'CONTINUO'
        && $entrada === '07:00'
        && $salida === '15:00'
    ) {
        return 'manana';
    }

    // Turno intermedio partido: 10:00-14:00 y 17:00-21:00.
    if (
        $horario->tipo_turno === 'PARTIDO'
        && $entrada === '10:00'
        && $salida === '14:00'
        && $entrada2 === '17:00'
        && $salida2 === '21:00'
    ) {
        return 'intermedio';
    }

    // Turno de tarde: termina a la hora de cierre de la sucursal.
    $cierre = $this->horaCierreSucursal($sucursal);

    if (
        $horario->tipo_turno === 'CONTINUO'
        && in_array($entrada, ['14:00', '15:00'], true)
        && $salida === $cierre
    ) {
        return 'tarde';
    }

    // Turno especial exclusivo de Santa Rosa 2.
    if (
        $this->esHorarioEspecial($horario)
        && $this->esSantaRosa2($sucursal)
    ) {
        return 'especial';
    }

    return 'invalido';
}

private function horaCierreSucursal(Sucursal $sucursal): string
{
    $tipoHorario = trim((string) $sucursal->tipo_horario);

    if ($tipoHorario === '07_22') {
        return '22:00';
    }

    if ($tipoHorario === '07_23') {
        return '23:00';
    }

    if ($tipoHorario === '08_20') {
        return '20:00';
    }

    // Valor predeterminado para sucursales sin configuración reconocida.
    return '23:00';
}


private function horariosDisponiblesParaSucursal(Sucursal $sucursal)
{
    $cierre = $this->horaCierreSucursal($sucursal);

    return Horario::where('estado', 1)
        ->when($sucursal->empresa_id, function ($query) use ($sucursal) {
            $query->where('empresa_id', $sucursal->empresa_id);
        })
        ->orderBy('hora_ingreso')
        ->get()
        ->filter(function (Horario $horario) use ($sucursal, $cierre) {
            if (
                $this->esHorarioEspecial($horario)
                && !$this->esSantaRosa2($sucursal)
            ) {
                return false;
            }

            // Evita ofrecer turnos de cierre que no corresponden al local.
            $salida = substr((string) $horario->hora_salida, 0, 5);

            if (
                !$this->esSantaRosa2($sucursal)
                && in_array($salida, ['22:00', '23:00'], true)
                && $salida !== $cierre
            ) {
                return false;
            }

            return true;
        })
        ->values();
}   


    
public function consulta(Request $request)
{
    $datos = $this->datosCalendarioConsulta($request);

    return view('asignacion-turnos.consulta', $datos);
}

public function exportarPdf(Request $request)
{
    $datos = $this->datosCalendarioConsulta($request);

    abort_unless($datos['sucursal'], 404, 'Selecciona una sucursal.');

    $pdf = Pdf::loadView('asignacion-turnos.pdf', $datos)
        ->setPaper('a4', 'landscape');

    $nombre = 'planificacion-' .
        \Illuminate\Support\Str::slug($datos['sucursal']->nombre) .
        '-' . $datos['fechaInicio']->format('Y-m-d') . '.pdf';

    return $pdf->download($nombre);
}

public function exportarExcel(Request $request)
{
    $datos = $this->datosCalendarioConsulta($request);

    abort_unless($datos['sucursal'], 404, 'Selecciona una sucursal.');

    $nombre = 'planificacion-' .
        \Illuminate\Support\Str::slug($datos['sucursal']->nombre) .
        '-' . $datos['fechaInicio']->format('Y-m-d') . '.xlsx';

    return Excel::download(
        new PlanificacionSemanalExport($datos),
        $nombre
    );
}


private function datosCalendarioConsulta(Request $request): array
{
    $inicio = $request->input(
        'inicio',
        now()->startOfWeek(Carbon::MONDAY)->toDateString()
    );

    $fechaInicio = Carbon::parse($inicio)->startOfWeek(Carbon::MONDAY);
    $fechaFin = $fechaInicio->copy()->endOfWeek(Carbon::SUNDAY);

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Determinar las sucursales que puede consultar el usuario
    |--------------------------------------------------------------------------
    */

    if ($user->hasRole('Colaborador')) {
        $trabajador = \App\Models\Trabajador::find($user->trabajador_id);

        if (!$trabajador || !$trabajador->sucursal_id) {
            abort(403, 'Tu cuenta no tiene una sucursal asignada.');
        }

        // El colaborador solo puede consultar su propia sucursal.
        $sucursales = Sucursal::where(
            'id',
            $trabajador->sucursal_id
        )->get();

        $sucursalId = $trabajador->sucursal_id;
    } else {
        // El administrador puede consultar todas las sucursales.
        $sucursales = Sucursal::orderBy('nombre')->get();

        $sucursalId = $request->input('sucursal_id');
    }

    $sucursal = $sucursalId
        ? $sucursales->firstWhere('id', (int) $sucursalId)
        : null;

    $turnos = collect();

    if ($sucursal) {
        $turnos = AsignacionTurno::with([
                'trabajador.cargo',
                'cargo',
                'horario',
            ])
            ->where('sucursal_id', $sucursal->id)
            ->whereBetween('fecha', [
                $fechaInicio->toDateString(),
                $fechaFin->toDateString(),
            ])
            ->orderBy('fecha')
            ->get()
            ->groupBy(function ($turno) {
                return Carbon::parse($turno->fecha)->toDateString();
            })
            ->map(function ($turnosDelDia) {
                return $turnosDelDia->sortBy(function ($turno) {
                    // Los descansos se muestran al final del día.
                    if ($turno->es_descanso || !$turno->horario) {
                        return '99:99';
                    }

                    return substr(
                        (string) $turno->horario->hora_ingreso,
                        0,
                        5
                    );
                })->values();
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Días de la semana
    |--------------------------------------------------------------------------
    */

    $dias = collect();

    for (
        $fecha = $fechaInicio->copy();
        $fecha->lte($fechaFin);
        $fecha->addDay()
    ) {
        $dias->push($fecha->copy());
    }

    /*
    |--------------------------------------------------------------------------
    | Sucursales que tienen planificación en la semana seleccionada
    |--------------------------------------------------------------------------
    */

    $idsConPlanificacion = PlanificacionSemanal::whereDate(
        'fecha_inicio',
        $fechaInicio->toDateString()
    )
        ->where('estado', 'registrada')
        ->pluck('sucursal_id');

    $idsConAsignaciones = AsignacionTurno::whereBetween('fecha', [
        $fechaInicio->toDateString(),
        $fechaFin->toDateString(),
    ])
        ->distinct()
        ->pluck('sucursal_id');

    $idsSucursalesPlanificadas = $idsConPlanificacion
        ->merge($idsConAsignaciones)
        ->unique()
        ->values();

    $sucursalesPlanificadas = $sucursales
        ->whereIn('id', $idsSucursalesPlanificadas)
        ->values();

    return compact(
        'sucursales',
        'sucursal',
        'sucursalId',
        'fechaInicio',
        'fechaFin',
        'dias',
        'turnos',
        'sucursalesPlanificadas'
    );
}


private function esSantaRosa2(Sucursal $sucursal): bool
{
    return mb_strtoupper(trim($sucursal->nombre)) === 'SANTA ROSA 2';
}

private function esHorarioEspecial(Horario $horario): bool
{
    return $horario->tipo_turno === 'CONTINUO'
        && substr((string) $horario->hora_ingreso, 0, 5) === '08:00'
        && substr((string) $horario->hora_salida, 0, 5) === '20:00';
}

}
