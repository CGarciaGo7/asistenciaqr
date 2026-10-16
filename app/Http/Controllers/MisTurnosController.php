<?php

namespace App\Http\Controllers;

use App\Models\AsignacionTurno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MisTurnosController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Obtener exclusivamente el trabajador vinculado a la cuenta.
        $trabajador = $user->trabajador()
    ->with(['cargo', 'sucursal', 'empresa'])
    ->first();

        if (!$trabajador) {
            return response()->view('mis-turnos.sin-vinculo', [], 403);
        }

        // Mes seleccionado: formato YYYY-MM.
        $mes = $request->query('mes', now()->format('Y-m'));

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
            abort(404);
        }

        $inicioMes = Carbon::createFromFormat('!Y-m', $mes)
            ->startOfMonth();

        $finMes = $inicioMes->copy()->endOfMonth();

        // El calendario empieza el lunes y termina el domingo.
        $inicioCalendario = $inicioMes->copy()
            ->startOfWeek(Carbon::MONDAY);

        $finCalendario = $finMes->copy()
            ->endOfWeek(Carbon::SUNDAY);

        // Consultar solo las asignaciones del trabajador autenticado.
        $asignaciones = AsignacionTurno::with([
                'horario',
                'sucursal',
                'cargo',
            ])
            ->where('trabajador_id', $trabajador->id)
            ->whereBetween('fecha', [
                $inicioCalendario->toDateString(),
                $finCalendario->toDateString(),
            ])
            ->orderBy('fecha')
            ->get()
            ->keyBy(fn ($asignacion) =>
                $asignacion->fecha->format('Y-m-d')
            );

        $dias = [];
        $cursor = $inicioCalendario->copy();

        while ($cursor->lte($finCalendario)) {
            $fecha = $cursor->copy();
            $asignacion = $asignaciones->get(
                $fecha->toDateString()
            );

            $dias[] = [
                'fecha' => $fecha,
                'asignacion' => $asignacion,
                'del_mes' => $fecha->month === $inicioMes->month
                    && $fecha->year === $inicioMes->year,
                'numero_semana' => $fecha->isoWeek(),
                'anio_semana' => $fecha->isoWeekYear(),
            ];

            $cursor->addDay();
        }

        return view('mis-turnos.index', [
            'trabajador' => $trabajador,
            'inicioMes' => $inicioMes,
            'finMes' => $finMes,
            'dias' => $dias,
            'mesAnterior' => $inicioMes->copy()
                ->subMonth()->format('Y-m'),
            'mesSiguiente' => $inicioMes->copy()
                ->addMonth()->format('Y-m'),
        ]);
    }
}
