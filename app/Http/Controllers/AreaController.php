<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{

     public function index(Request $request)
    {
        $total = Area::count();
        $activas = Area::where('estado', 1)->count();
        $inactivas = Area::where('estado', 0)->count();
        $areas = Area::orderBy('nombre')
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return view('areas.index', compact(
            'total',
            'activas',
            'inactivas',
            'areas'
        ));
    }

    public function create()
    {
        return view('areas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('areas', 'nombre'),
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
            'estado' => [
                'required',
                'boolean',
            ],
        ], [
            'nombre.required' => 'Debe ingresar el nombre del área.',
            'nombre.unique' => 'El nombre del área ya existe.',
        ]);

        DB::beginTransaction();

        try {

            Area::create([
                'nombre' => strtoupper(trim($request->nombre)),
                'descripcion' => $request->descripcion
                    ? strtoupper(trim($request->descripcion))
                    : null,
                'estado' => $request->estado,
            ]);

            DB::commit();

            return redirect()
                ->route('areas.index')
                ->with('success', 'Área registrada correctamente.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage(),
                ]);
        }
    }

    public function validarNombre(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'id' => 'nullable|integer|exists:areas,id',
        ]);

        $nombre = strtoupper(trim($request->nombre));

        $query = Area::where('nombre', $nombre);

        if ($request->filled('id')) {
            $query->where('id', '<>', $request->id);
        }

        return response()->json([
            'existe' => $query->exists(),
        ]);
    }

    public function show(Area $area)
    {
        return view('areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('areas', 'nombre')
                    ->ignore($area->id),
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
            'estado' => [
                'required',
                'boolean',
            ],
        ], [
            'nombre.required' => 'Debe ingresar el nombre del área.',
            'nombre.unique' => 'El nombre del área ya existe.',
        ]);

        DB::beginTransaction();

        try {

            $area->update([
                'nombre' => strtoupper(trim($request->nombre)),
                'descripcion' => $request->descripcion
                    ? strtoupper(trim($request->descripcion))
                    : null,
                'estado' => $request->estado,
            ]);

            DB::commit();

            return redirect()
                ->route('areas.index')
                ->with('success', 'Área actualizada correctamente.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage(),
                ]);
        }
    }

    public function destroy(Area $area)
    {
        $area->estado = !$area->estado;
        $area->save();

        return redirect()
            ->route('areas.index')
            ->with('success', 'Estado actualizado correctamente.');
    }
}
