<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Empresa;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\Horario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class TrabajadorController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $totalTrabajadores = Trabajador::count();
        $activos = Trabajador::where('estado', 1)->count();
        $inactivos = Trabajador::where('estado', 0)->count();

        $empresas = Empresa::where('estado', 1)->orderBy('razon_social')->get();
        $sucursales = Sucursal::where('estado', 1)->orderBy('nombre')->get();
        $areas = Area::where('estado', 1)->orderBy('nombre')->get();
        $cargos = Cargo::where('estado', 1)->orderBy('nombre')->get();
        $trabajadores = Trabajador::with(['empresa','sucursal','area','cargo' ])
        ->when($buscar, function ($query) use ($buscar) {

            $query->where(function ($q) use ($buscar) {

                $q->where('codigo', 'like', "%{$buscar}%")
                    ->orWhere('dni', 'like', "%{$buscar}%")
                    ->orWhere('primer_nombre', 'like', "%{$buscar}%")
                    ->orWhere('segundo_nombre', 'like', "%{$buscar}%")
                    ->orWhere('primer_apellido', 'like', "%{$buscar}%")
                    ->orWhere('segundo_apellido', 'like', "%{$buscar}%");
            });
        })
        ->orderBy('id', 'asc')
        ->get();

        return view('trabajadores.index', compact(
            'trabajadores',
            'buscar',
            'totalTrabajadores',
            'activos',
            'inactivos',
            'empresas',
            'sucursales',
            'areas',
            'cargos'
        ));
    }

    public function create()
    {
        $trabajador = new Trabajador();

        $empresas = Empresa::where('estado', 1)->orderBy('razon_social')->get();
        $sucursales = Sucursal::where('estado', 1)->orderBy('nombre')->get();
        $areas = Area::where('estado', 1)->orderBy('nombre')->get();
        $cargos = Cargo::where('estado', 1)->orderBy('nombre')->get();

        return view('trabajadores.create', compact(
            'trabajador',
            'empresas',
            'sucursales',
            'areas',
            'cargos'
        ));
    }

    /* Validar si el DNI ya existe*/
    public function validarDni($dni): JsonResponse
    {
        $existe = Trabajador::where('dni', $dni)->exists();

        return response()->json([
            'existe' => $existe
        ]);
    }

    public function generarCodigo(Request $request)
    {
        $primerNombre = strtoupper(trim($request->primer_nombre ?? ''));
        $primerApellido = strtoupper(trim($request->primer_apellido ?? ''));

        if (
            $primerNombre === '' ||
            $primerApellido === ''
        ) {
            return response()->json([
                'codigo' => ''
            ]);
        }

        $inicialNombre = substr($primerNombre,0,1);
        $primerapellidoCompleto = preg_replace('/\s+/','', $primerApellido);
        $segundoApellidocompleto = preg_replace('/\s+/','', $segundo_apellido ?? '');
        $primerasDos = substr($segundoApellidocompleto,0,2);
        $ultimoId = Trabajador::max('id');
        $siguienteId = $ultimoId
            ? $ultimoId + 1
            : 1;

        $correlativo = str_pad($siguienteId,3,'0',STR_PAD_LEFT);

        $codigo =
            $inicialNombre .
            $primerapellidoCompleto .
            $primerasDos .
            $correlativo;

        return response()->json([
            'codigo' => $codigo,
            'siguiente_id' => $siguienteId
        ]);
    }

    public function areas(Sucursal $sucursal)
{
    $areas = Area::where('sucursal_id', $sucursal->id)
        ->where('estado', 1)
        ->orderBy('nombre')
        ->get(['id', 'nombre']);

    return response()->json($areas);
}

    public function cargos($empresa)
    {
        return Cargo::where('empresa_id', $empresa)
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate(['empresa_id' => ['required','exists:empresas,id'],
            'sucursal_id' => ['required','exists:sucursals,id'],
            'area_id' => ['required','exists:areas,id'],
            'cargo_id' => ['required','exists:cargos,id'],
            'dni' => ['required','digits:8','regex:/^[0-9]{8}$/','unique:trabajadors,dni'],
            'primer_nombre' => ['required','string','max:150'],
            'segundo_nombre' => ['nullable','string','max:150'],
            'primer_apellido' => ['required','string','max:150'],
            'segundo_apellido' => ['required','string','max:150'],
            'sexo' => ['required','in:M,F'],
            'fecha_ingreso' => ['required','date'],
        ]);

        DB::beginTransaction();
        $foto = null;

        try {
            /* Guardar fotografía */
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto')->store('trabajadores/fotos','public');}

            /* Primero creamos el trabajador con un código temporal.
             * Esto permite obtener el ID real generado por la base de datos.  */
            $trabajador = Trabajador::create([
                'codigo' => 'TEMP-' . uniqid(),
                'empresa_id' => $request->empresa_id,
                'sucursal_id' => $request->sucursal_id,
                'area_id' => $request->area_id,
                'cargo_id' => $request->cargo_id,
                'dni' => $request->dni,
                'primer_nombre' => strtoupper(trim($request->primer_nombre)),
                'segundo_nombre' => $request->segundo_nombre
                    ? strtoupper(trim($request->segundo_nombre)) : null,
                'primer_apellido' => strtoupper(trim($request->primer_apellido)),
                'segundo_apellido' => strtoupper(trim($request->segundo_apellido)),
                'sexo' => $request->sexo,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'fecha_ingreso' => $request->fecha_ingreso,
                'foto' => $foto,
                'estado' => 1,
            ]);


            /* GENERAR CÓDIGO DEFINITIVO */

            $inicialNombre = substr($trabajador->primer_nombre,0,1);
            $primerpellido = preg_replace('/\s+/','', $trabajador->segundo_apellido );
            $primerasDos = substr($primerpellido,0,2);

            /* Utilizamos el ID REAL */
            $correlativo = str_pad($trabajador->id,3,'0', STR_PAD_LEFT);
            $codigo =
                $inicialNombre .
                $primerpellido .
                $primerasDos .
                $correlativo;

            /* Actualizar el código definitivo */
            $trabajador->update([
                'codigo' => $codigo
            ]);

            DB::commit();

            return redirect()
                ->route('trabajadores.index')
                ->with(
                    'success',
                    'Trabajador registrado correctamente.'
                );

        } catch (\Exception $e) {

            DB::rollBack();
            if (
                $foto &&
                Storage::disk('public')->exists($foto)
            ) {
                Storage::disk('public')->delete($foto);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage()
                ]);
        }
    }

public function show(Trabajador $trabajador)
{
    $trabajador->load([
        'empresa',
        'sucursal',
        'area',
        'cargo'
    ]);

    return response()->json([
        'id' => $trabajador->id,
        'codigo' => $trabajador->codigo,
        'dni' => $trabajador->dni,
        'primer_nombre' => $trabajador->primer_nombre,
        'segundo_nombre' => $trabajador->segundo_nombre,
        'primer_apellido' => $trabajador->primer_apellido,
        'segundo_apellido' => $trabajador->segundo_apellido,
        'sexo' => $trabajador->sexo,
        'telefono' => $trabajador->telefono,
        'direccion' => $trabajador->direccion,

        'fecha_ingreso' => $trabajador->fecha_ingreso
            ? $trabajador->fecha_ingreso->format('Y-m-d')
            : null,

        'fecha_cese' => $trabajador->fecha_cese
            ? $trabajador->fecha_cese->format('Y-m-d')
            : null,

        'estado' => $trabajador->estado,

        // Identificadores para seleccionar los campos del modal
        'empresa_id' => $trabajador->empresa_id,
        'sucursal_id' => $trabajador->sucursal_id,
        'area_id' => $trabajador->area_id,
        'cargo_id' => $trabajador->cargo_id,

        'foto' => $trabajador->foto
            ? asset('storage/' . $trabajador->foto)
            : null,

        // Nombres para mostrar información descriptiva
        'empresa' => $trabajador->empresa
            ? $trabajador->empresa->razon_social
            : null,

        'sucursal' => $trabajador->sucursal
            ? $trabajador->sucursal->nombre
            : null,

        'area' => $trabajador->area
            ? $trabajador->area->nombre
            : null,

        'cargo' => $trabajador->cargo
            ? $trabajador->cargo->nombre
            : null,
    ]);
}

    public function edit(Trabajador $trabajador)
    {
        $empresas = Empresa::where('estado', 1)->orderBy('razon_social')->get();
        $sucursales = Sucursal::where('estado', 1)->orderBy('nombre')->get();
        $areas = Area::where('empresa_id', $trabajador->empresa_id)
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $cargos = Cargo::where('empresa_id', $trabajador->empresa_id)
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view(
            'trabajadores.edit',
            compact('trabajador','empresas','sucursales','areas','cargos')
        );
    }

    public function update(Request $request, Trabajador $trabajador )
     {
        $request->validate([
            'empresa_id' => ['required','exists:empresas,id'],
            'sucursal_id' => ['required','exists:sucursals,id'],
            'area_id' => ['required','exists:areas,id'],
            'cargo_id' => ['required','exists:cargos,id'],
            'dni' => ['required','digits:8','regex:/^[0-9]{8}$/','unique:trabajadors,dni,' . $trabajador->id],
            'primer_nombre' => ['required','string','max:150'],
            'segundo_nombre' => ['nullable','string','max:150'],
            'primer_apellido' => ['required','string','max:150'],
            'segundo_apellido' => ['required','string','max:150'],
            'sexo' => ['required', 'in:M,F'],
            'telefono' => ['nullable','string','max:20'],
            'direccion' => ['nullable','string','max:255'],
            'foto' => ['nullable','image','mimes:jpg,jpeg,png','max:2048' ],
            'fecha_ingreso' => ['required','date'],
            'estado' => ['required', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            /* Actualizar fotografía  */
            if ($request->hasFile('foto')) {
                if (
                    $trabajador->foto &&
                    Storage::disk('public')->exists(
                        $trabajador->foto
                    )
                ) {
                    Storage::disk('public')->delete(
                        $trabajador->foto
                    );
                }
                $trabajador->foto = $request->file('foto')->store('trabajadores/fotos','public' );
            }

            /* Actualizar datos */
            $trabajador->empresa_id = $request->empresa_id;
            $trabajador->sucursal_id = $request->sucursal_id;
            $trabajador->area_id = $request->area_id;
            $trabajador->cargo_id = $request->cargo_id;
            $trabajador->dni = $request->dni;
            $trabajador->primer_nombre = strtoupper(trim($request->primer_nombre) );
            $trabajador->segundo_nombre = $request->segundo_nombre
                    ? strtoupper(
                        trim($request->segundo_nombre)
                    )
                    : null;

            $trabajador->primer_apellido = strtoupper(trim($request->primer_apellido));
            $trabajador->segundo_apellido = strtoupper(trim($request->segundo_apellido) );
            $trabajador->sexo = $request->sexo;
            $trabajador->telefono = $request->telefono;
            $trabajador->direccion = $request->direccion;
            $trabajador->fecha_ingreso = $request->fecha_ingreso;
            $trabajador->estado = $request->estado;

            /*
             * El código se vuelve a generar si se
             * modifican nombre o segundo apellido.
             */
            $nombreAnterior = $trabajador->primer_nombre;
            $segundoApellidoAnterior = $trabajador->segundo_apellido;

            // Asignar los nuevos datos
            $trabajador->primer_nombre = strtoupper(trim($request->primer_nombre));
            $trabajador->segundo_nombre = $request->segundo_nombre
                ? strtoupper(trim($request->segundo_nombre))
                : null;

            $trabajador->primer_apellido = strtoupper(
                trim($request->primer_apellido)
            );
            $trabajador->segundo_apellido = strtoupper(
                trim($request->segundo_apellido)
            );

            // Generar nuevamente el código solo si corresponde
            if (
                $nombreAnterior !== $trabajador->primer_nombre ||
                $segundoApellidoAnterior !== $trabajador->segundo_apellido
                ) {

            // Inicial del primer nombre
            $inicialNombre = substr(
            $trabajador->primer_nombre,0,1);

            // Primer apellido completo, sin espacios
                $primerApellido = preg_replace('/\s+/','',$trabajador->primer_apellido);

            // Segundo apellido sin espacios
                $segundoApellido = preg_replace('/\s+/','', $trabajador->segundo_apellido );

            // Primeras dos letras del segundo apellido
                $primerasDos = substr($segundoApellido,0,2);

            // ID con 3 dígitos
                $correlativo = str_pad($trabajador->id,3,'0',STR_PAD_LEFT);

            // Código final
                $trabajador->codigo =
                $inicialNombre .
                $primerApellido .
                $primerasDos .
                $correlativo; }
$trabajador->save();

            DB::commit();
            return redirect()
                ->route('trabajadores.index')
                ->with('success', 'Operación Exitosa');

    } catch (\Exception $e) {

            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage()
                ]);
        }
    }

    public function destroy(Trabajador $trabajador)
    {
        if ($trabajador->foto) {

            Storage::disk('public')->delete(
                $trabajador->foto
            );
        }

        $trabajador->delete();

        return redirect()
            ->route('trabajadores.index')
            ->with(
                'success',
                'Trabajador eliminado correctamente.'
            );
    }
}