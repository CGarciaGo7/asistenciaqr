<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SucursalController extends Controller
{
   
    public function index(Request $request)
    {
        $total = Sucursal::count();
        $activas = Sucursal::where('estado', 1)->count();
        $inactivas = Sucursal::where('estado', 0)->count();
    
        $buscar = trim($request->buscar);
        $sucursales = Sucursal::query()

        ->when($buscar, function ($query) use ($buscar) {

            $query->where('nombre', 'like', "%{$buscar}%");

        })

        ->orderBy('nombre')
        ->paginate(10)
        ->withQueryString();

        return view('sucursales.index', compact('sucursales', 'buscar','total','activas','inactivas'));
        
    }
    
    public function create()
    {
        return view('sucursales.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'direccion' => 'nullable|string|max:255',
        'telefono' => 'nullable|digits:9',
        'correo' => [
            'nullable',
            'string',
            'max:50',
            'regex:/^[a-zA-Z0-9._-]+$/',
        ],
        'tipo_horario' => ['required','in:07_23,07_22,08_20',],
    ], [
        'nombre.required' => 'Debe ingresar el nombre.',
        'telefono.digits' => 'El teléfono debe contener exactamente 9 dígitos.',
        'correo.regex' => 'Ingrese únicamente el identificador del correo sin espacios.',
        'tipo_horario.in' =>'Seleccione una modalidad de horario válida.',
        'tipo_horario.required' =>'Debe seleccionar el horario de atención de la sucursal.',
    ]);

    DB::beginTransaction();

    try {

        $correo = $request->filled('correo')
            ? 'solidaria.' . strtolower(trim($request->correo)) . '@farmaciassolidaria.com'
            : null;

        // Verificar correo solamente si se ingresó
        if ($correo) {

            $correoExiste = Sucursal::where('correo', $correo)->exists();

            if ($correoExiste) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'correo' => 'Este correo ya está registrado en otra sucursal.'
                    ])
                    ->with('operacion_sucursal', 'crear');
            }
        }

        Sucursal::create([
            'empresa_id' => 1,
            'nombre' => strtoupper(trim($request->nombre)),
            'direccion' => $request->filled('direccion')
                ? strtoupper(trim($request->direccion))
                : null,
            'telefono' => $request->filled('telefono')
                ? trim($request->telefono)
                : null,
            'correo' => $correo,
            'tipo_horario' => $request->tipo_horario,
            'estado' => 1,
        ]);

        DB::commit();

        return redirect()
            ->route('sucursales.index')
            ->with('success', 'Sucursal registrada correctamente.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'error' => $e->getMessage()
            ])
            ->with('operacion_sucursal', 'crear');
    }
}
    /**
     * Display the specified resource.
     */
    public function show(Sucursal $sucursal)
    {
        return view('sucursales.show', compact('sucursal'));
    }

    
    public function edit(Sucursal $sucursal)
    {
        return view('sucursales.edit', compact('sucursal'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Sucursal $sucursal)
{
    /* Validación */

    $validator = Validator::make(
        $request->all(),
        [
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => ['nullable','digits:9',],
            'correo' => ['nullable','string','max:50','regex:/^[a-zA-Z0-9._-]+$/',],
            'tipo_horario' => ['required','in:07_23,07_22,08_20',],
        ],
        [
            'nombre.required' => 'Debe ingresar el nombre de la sucursal.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',
            'telefono.digits' => 'El teléfono debe contener exactamente 9 dígitos.',
            'correo.max' => 'El identificador del correo no puede superar los 50 caracteres.',
            'correo.regex' =>
                'Ingrese únicamente letras, números, puntos, guiones o guiones bajos.',
            'tipo_horario.required' => 'Debe seleccionar el horario de atención de la sucursal.',
            'tipo_horario.in' => 'El tipo de horario seleccionado no es válido.',
        ]
    );

    /* Si existen errores de validación */

    if ($validator->fails()) {

        return back()
            ->withInput()
            ->withErrors($validator)
            ->with('operacion_sucursal', 'editar')
            ->with('editar_sucursal_id', $sucursal->id);
    }

    /* Construir correo completo */

    $correoNuevo = $request->filled('correo')
        ? 'solidaria.' . strtolower(trim($request->correo)) . '@farmaciassolidaria.com'
        : null;

    /* Validar correo duplicado */

    if ($correoNuevo) {

        $correoExiste = Sucursal::where('correo', $correoNuevo)
            ->where('id', '!=', $sucursal->id)
            ->exists();

        if ($correoExiste) {

            return back()
                ->withInput()
                ->withErrors([
                    'correo' => 'Este correo ya está registrado en otra sucursal.'
                ])
                ->with('operacion_sucursal', 'editar')
                ->with('editar_sucursal_id', $sucursal->id);
        }
    }

    /* Actualizar sucursal*/

    DB::beginTransaction();

    try {

        $sucursal->update([
            'nombre' => strtoupper(trim($request->nombre)),
            'direccion' => $request->filled('direccion')
                ? strtoupper(trim($request->direccion))
                : null,
            'telefono' => $request->filled('telefono')
                ? trim($request->telefono)
                : null,
            'correo' => $correoNuevo,
            'tipo_horario' => $request->tipo_horario,
        ]);

        DB::commit();

        return redirect()
            ->route('sucursales.index')
            ->with('success', 'Operación Exitosa');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'error' => 'No fue posible actualizar la sucursal. ' . $e->getMessage()
            ])
            ->with('operacion_sucursal', 'editar')
            ->with('editar_sucursal_id', $sucursal->id);
    }
}
       /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sucursal $sucursal)
    {
        $sucursal->estado=!$sucursal->estado;
        $sucursal->save();

        return redirect()
            ->route('sucursales.index')
            ->with('success','Estado actualizado correctamente.');
    }
}