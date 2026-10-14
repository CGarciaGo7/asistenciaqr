<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CargoController extends Controller
{
   
    public function index(Request $request)
    {

        $total = Cargo::count();
        $activas = Cargo::where('estado', 1)->count();
        $inactivas = Cargo::where('estado', 0)->count();

        $buscar = trim($request->buscar);
        $cargos = Cargo::query()

        ->when($buscar, function ($query) use ($buscar) {

            $query->where('nombre', 'like', "%{$buscar}%");

        })

        ->orderBy('nombre')
        ->paginate(10)
        ->withQueryString();

        return view('cargos.index', compact('cargos', 'buscar','total','activas','inactivas')); 
    }
    
    public function create()
    {
        return view('cargos.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'direccion' => 'nullable|string|max:255',
        ]);

    DB::beginTransaction();

    try {


        Cargo::create([
            'nombre' => strtoupper(trim($request->nombre)),
            'descripcion' => $request->filled('descripcion')
                ? strtoupper(trim($request->descripcion))
                : null,
            'estado' => 1,
        ]);

        DB::commit();

        return redirect()
            ->route('cargos.index')
            ->with('success', 'Cargo registrado correctamente.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'error' => $e->getMessage()
            ])
            ->with('operacion_cargo', 'crear');
    }
}
    /**
     * Display the specified resource.
     */
    public function show(Cargo $cargo)
    {
        return view('cargos.show', compact('cargo'));
    }

    
    public function edit(Cargo $cargo)
    {
        return view('cargos.edit', compact('cargo'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Cargo $cargo)
{
    /* Validación */

    $validator = Validator::make(
        $request->all(),
        [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',  
        ],
    );

    /* Actualizar cargo*/

    DB::beginTransaction();

    try {
        $cargo->update([
            'nombre' => strtoupper(trim($request->nombre)),
            'descripcion' => $request->filled('descripcion')
                ? strtoupper(trim($request->descripcion))
                : null,
        ]);

        DB::commit();

        return redirect()
            ->route('cargos.index')
            ->with('success', 'Operación Exitosa');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'error' => 'No fue posible actualizar el cargo. ' . $e->getMessage()
            ])
            ->with('operacion_cargo', 'editar')
            ->with('editar_cargo_id', $cargo->id);
    }
}
    // Continúa aquí el resto de tu update...

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cargo $cargo)
    {
        $cargo->estado=!$cargo->estado;
        $cargo->save();

        return redirect()
            ->route('cargos.index')
            ->with('success','Estado actualizado correctamente.');
    }
}