<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
   
    public function index(Request $request)
    {
        $buscar = trim($request->buscar);

        $empresas = Empresa::query()

        ->when($buscar, function ($query) use ($buscar) {

            $query->where('ruc', 'like', "%{$buscar}%")
                ->orWhere('razon_social', 'like', "%{$buscar}%")
                ->orWhere('nombre_comercial', 'like', "%{$buscar}%");

        })

        ->orderBy('nombre_comercial')
        ->paginate(10)
        ->withQueryString();

        return view('empresas.index', compact('empresas', 'buscar'));

    }
    
    public function validarRuc($ruc)
    {
        $existe = Empresa::where('ruc', $ruc)->exists();

        return response()->json([
            'existe' => $existe
        ]);
    }


    public function create()
    {
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
        'ruc' => 'required|digits:11|unique:empresas,ruc',
        'razon_social' => 'required|string|max:255',
        'nombre_comercial' => 'required|string|max:255',
        'direccion' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'correo' => 'nullable|email|max:100',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ],[
        'ruc.required' => 'Debe ingresar el RUC.',
        'ruc.digits' => 'El RUC debe tener 11 dígitos.',
        'ruc.unique' => 'El RUC ya existe.',
        'correo.email' => 'Ingrese un correo válido.'
    ]);

    DB::beginTransaction();

    try {

        $logo = null;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store(
                'empresas/logos',
                'public'
            );
        }

        Empresa::create([
            'ruc' => $request->ruc,
            'razon_social' => strtoupper(trim($request->razon_social)),
            'nombre_comercial' => strtoupper(trim($request->nombre_comercial)),
            'direccion' => strtoupper(trim($request->direccion)),
            'telefono' => $request->telefono,
            'correo' => strtolower(trim($request->correo)),
            'logo' => $logo,
            'estado' => 1
        ]);

        DB::commit();

        return redirect()
            ->route('empresas.index')
            ->with('success','Empresa registrada correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }

        return back()
            ->withInput()
            ->withErrors([
                'error' => $e->getMessage()
            ]);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        return view('empresas.show', compact('empresa'));
    }

    
    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        
        $request->validate([
        'ruc' => 'required|digits:11|unique:empresas,ruc,' . $empresa->id,
        'razon_social' => 'required|string|max:255',
        'nombre_comercial' => 'required|string|max:255',
        'direccion' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'correo' => 'nullable|email|max:100',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    DB::beginTransaction();

    try {
        $logo = $empresa->logo;
        if ($request->hasFile('logo')) {
            if ($empresa->logo &&
                Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }

            $logo = $request->file('logo')->store(
                'empresas/logos',
                'public'
            );
        }

        $empresa->update([
            'ruc' => $request->ruc,
            'razon_social' => strtoupper(trim($request->razon_social)),
            'nombre_comercial' => strtoupper(trim($request->nombre_comercial)),
            'direccion' => strtoupper(trim($request->direccion)),
            'telefono' => $request->telefono,
            'correo' => strtolower(trim($request->correo)),
            'logo' => $logo
        ]);

        DB::commit();

        return redirect()
            ->route('empresas.index')
            ->with('success','Empresa actualizada correctamente.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'error' => $e->getMessage()
            ]);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->estado=!$empresa->estado;
        $empresa->save();

        return redirect()
            ->route('empresas.index')
            ->with('success','Estado actualizado correctamente.');
    }
}
