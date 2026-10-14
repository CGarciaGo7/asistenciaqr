<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class HorarioController extends Controller
{

    public function index(Request $request)
    {
        $total = Horario::count();
        $activos = Horario::where('estado',1)->count();
        $inactivos = Horario::where('estado',0)->count();
        $empresas = Empresa::where('estado',1)
                    ->orderBy('nombre_comercial')
                    ->get();
        $horarios = Horario::with('empresa')
        ->when($request->empresa,function($q) use($request){
            $q->where('empresa_id',$request->empresa);
        })

        ->when($request->buscar,function($q) use($request){
            $q->where('nombre','like','%'.$request->buscar.'%');
        })
        ->oldest('id')
        ->paginate(10)
        ->withQueryString();

        return view('horarios.index',compact(
            'horarios',
            'empresas',
            'total',
            'activos',
            'inactivos'
        )); 
    }

    public function create()
    {
        $empresas = Empresa::where('estado',1)
        ->orderBy('nombre_comercial')
        ->get();

        return view('horarios.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|max:100',
            'tipo_turno' => 'required',
            'hora_ingreso' => 'required',
            'hora_salida' => 'required',
            'hora_ingreso2' => 'nullable',
            'hora_salida2' => 'nullable',
            'refrigerio' => 'required|integer|min:0',
            'tolerancia' => 'required|integer|min:0|max:60',
            'estado' => 'required'
        ]);

    DB::beginTransaction();

    try{

        Horario::create([
            'empresa_id'=>$request->empresa_id,
            'nombre'=>strtoupper($request->nombre),
            'tipo_turno'=>$request->tipo_turno,
            'hora_ingreso'=>$request->hora_ingreso,
            'hora_salida'=>$request->hora_salida,
            'hora_ingreso2'=>$request->tipo_turno=='PARTIDO'
                    ?$request->hora_ingreso2
                    :null,

            'hora_salida2'=>$request->tipo_turno=='PARTIDO'
                    ?$request->hora_salida2
                    :null,

            'refrigerio'=>$request->refrigerio,
            'tolerancia'=>$request->tolerancia,
            'observacion' => $request->observacion
                        ? strtoupper($request->observacion): null,
            'estado'=>$request->estado

        ]);

        DB::commit();

        return redirect()
            ->route('horarios.index')
            ->with('success','Horario registrado correctamente.');

    }catch(\Exception $e){

        DB::rollBack();

        return back()
            ->withInput()
            ->with('error',$e->getMessage());

    }
    }

    public function show(Horario $horario)
    {
        $horario->load('empresa');

        return view('horarios.show', compact('horario'));
    }

    public function edit(Horario $horario)
    {
        $empresas = Empresa::where('estado', 1)
        ->orderBy('nombre_comercial')
        ->get();

        return view('horarios.edit', compact(
            'horario',
            'empresas'
    ));
    }

    public function update(Request $request, Horario $horario)
    {
        $request->validate([
        'empresa_id'     => 'required|exists:empresas,id',
        'nombre'         => 'required|max:100',
        'tipo_turno'     => 'required|in:CONTINUO,PARTIDO',
        'hora_ingreso'   => 'required',
        'hora_salida'    => 'required',
        'refrigerio'     => 'required|integer|min:0',
        'tolerancia'     => 'required|integer|min:0|max:60',
        'estado'         => 'required|boolean',
        'observacion'    => 'nullable|max:500',
    ]);

    if ($request->tipo_turno == 'PARTIDO') {
        $request->validate([
            'hora_ingreso2' => 'required',
            'hora_salida2'  => 'required',

        ]);

    }

    DB::beginTransaction();

    try {

        $horario->update([
            'empresa_id'     => $request->empresa_id,
            'nombre'         => strtoupper($request->nombre),
            'tipo_turno'     => $request->tipo_turno,
            'hora_ingreso'   => $request->hora_ingreso,
            'hora_salida'    => $request->hora_salida,
            'hora_ingreso2'  => $request->tipo_turno == 'PARTIDO'
                                ? $request->hora_ingreso2
                                : null,

            'hora_salida2'   => $request->tipo_turno == 'PARTIDO'
                                ? $request->hora_salida2
                                : null,
            'refrigerio'     => $request->refrigerio,
            'tolerancia'     => $request->tolerancia,
            'observacion'    => strtoupper($request->observacion),
            'estado'         => $request->estado,

        ]);

        DB::commit();

        return redirect()
                ->route('horarios.index')
                ->with('success', 'Horario actualizado correctamente.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Horario $horario)
    {
        try {

        $horario->update([
            'estado' => $horario->estado ? 0 : 1
        ]);

        return redirect()
            ->route('horarios.index')
            ->with('success', 'Estado del horario actualizado correctamente.');

    } catch (\Exception $e) {

        return redirect()
            ->route('horarios.index')
            ->with('error', $e->getMessage());

    }
    }
}
