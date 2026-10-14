<?php

namespace App\Http\Controllers;

use App\Models\AsignacionHorario;
use App\Models\Horario;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionHorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $asignaciones = AsignacionHorario::with([
                'trabajador',
                'trabajador.empresa',
                'trabajador.area',
                'trabajador.cargo',
                'horario'
            ])
            ->when($buscar, function ($query) use ($buscar) {

                $query->whereHas('trabajador', function ($q) use ($buscar) {

                    $q->where('codigo', 'LIKE', "%{$buscar}%")
                    ->orWhere('dni', 'LIKE', "%{$buscar}%")
                    ->orWhere('nombres', 'LIKE', "%{$buscar}%")
                    ->orWhere('apellidos', 'LIKE', "%{$buscar}%");

                });

            })
            ->orderByDesc('id')
            ->paginate(10);

        return view(
            'asignacion-horarios.index',
            compact('asignaciones')
        );
    }

    public function create()
    {
        $trabajadores = Trabajador::where('estado',1)
                            ->orderBy('apellidos')
                            ->get();

        $horarios = Horario::where('estado',1)
                        ->orderBy('nombre')
                        ->get();

        return view(
            'asignacion-horarios.create',
            compact(
                'trabajadores',
                'horarios'
            )
        );
    }

    public function informacionTrabajador(Trabajador $trabajador)
{
    $trabajador->load([
        'empresa',
        'area',
        'cargo',
        'horario'
    ]);

    return response()->json($trabajador);
}


    public function store(Request $request)
    {
        $request->validate([
            'trabajador_id'=>'required|exists:trabajadors,id',
            'horario_id'=>'required|exists:horarios,id',
            'fecha_inicio'=>'required|date',
            'fecha_fin'=>'nullable|date|after_or_equal:fecha_inicio',
            'observacion'=>'nullable|max:500'
        ]);

        DB::beginTransaction();

        try {
                $conflicto = AsignacionHorario::where('trabajador_id', $request->trabajador_id)
                    ->where(function ($q) use ($request) {
                        $q->whereNull('fecha_fin')
                        ->orWhere('fecha_fin', '>=', $request->fecha_inicio);
                    })
                    ->exists();

                if ($conflicto) {
                    return back()
                        ->withInput()
                        ->with('error', 'El trabajador ya posee una asignación vigente.');
                }
            /*
            |----------------------------------------------
            | Cerrar asignación anterior
            |----------------------------------------------
            */

            AsignacionHorario::where('trabajador_id',$request->trabajador_id)
                ->where('estado',1)
                ->update([
                    'estado'=>0,
                    'fecha_fin'=>date(
                        'Y-m-d',
                        strtotime($request->fecha_inicio.' -1 day')
                    )
                ]);

            /*
            |----------------------------------------------
            | Nueva asignación
            |----------------------------------------------
            */

            AsignacionHorario::create([
                'trabajador_id'=>$request->trabajador_id,
                'horario_id'=>$request->horario_id,
                'fecha_inicio'=>$request->fecha_inicio,
                'fecha_fin'=>$request->fecha_fin,
                'observacion'=>strtoupper($request->observacion),
                'estado'=>1

            ]);

            DB::commit();

            return redirect()
                ->route('asignacion-horarios.index')
                ->with(
                    'success',
                    'Horario asignado correctamente.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function show(AsignacionHorario $asignacionHorario)
    {
        $asignacionHorario->load([
            'trabajador',
            'trabajador.empresa',
            'trabajador.cargo',
            'horario'
        ]);

        return view(
            'asignacion_horarios.show',
            compact('asignacionHorario')
        );
    }

    public function edit(AsignacionHorario $asignacionHorario)
    {
        $trabajadores = Trabajador::where('estado',1)
                            ->orderBy('apellidos')
                            ->get();

        $horarios = Horario::where('estado',1)
                        ->orderBy('nombre')
                        ->get();

        return view(
            'asignacion_horarios.edit',
            compact(
                'asignacionHorario',
                'trabajadores',
                'horarios'
            )
        );
    }

    public function update(Request $request, AsignacionHorario $asignacionHorario)
    {
        $request->validate([
            'trabajador_id'=>'required',
            'horario_id'=>'required',
            'fecha_inicio'=>'required',
            'fecha_fin'=>'nullable',
            'observacion'=>'nullable'
        ]);

        $asignacionHorario->update([
            'trabajador_id'=>$request->trabajador_id,
            'horario_id'=>$request->horario_id,
            'fecha_inicio'=>$request->fecha_inicio,
            'fecha_fin'=>$request->fecha_fin,
            'observacion'=>strtoupper($request->observacion),
            'estado'=>$request->estado
        ]);

        return redirect()
            ->route('asignacion-horarios.index')
            ->with(
                'success',
                'Asignación actualizada correctamente.'
            );
    }

    public function destroy(AsignacionHorario $asignacionHorario)
    {
        $asignacionHorario->update([
            'estado'=>!$asignacionHorario->estado
        ]);

        return redirect()
            ->route('asignacion-horarios.index')
            ->with(
                'success',
                'Estado actualizado correctamente.'
            );
    }
    
}
