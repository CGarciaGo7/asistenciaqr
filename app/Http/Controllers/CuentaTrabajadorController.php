<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CuentaTrabajadorController extends Controller
{
    public function index()
    {
        $trabajadores = Trabajador::with(['sucursal', 'cargo', 'user'])
            ->orderBy('primer_apellido')
            ->orderBy('primer_nombre')
            ->get();

        return view('cuentas-trabajadores.index', compact('trabajadores'));
    }

    
public function store(Request $request)
{
    $datos = $request->validate([
        'trabajador_id' => [
            'required',
            'integer',
            'exists:trabajadors,id',
        ],
    ]);

    DB::transaction(function () use ($datos) {
        $trabajador = Trabajador::whereKey(
            $datos['trabajador_id']
        )->lockForUpdate()->firstOrFail();

        if ($trabajador->user()->exists()) {
            throw ValidationException::withMessages([
                'trabajador_id' =>
                    'Este trabajador ya tiene una cuenta de acceso.',
            ]);
        }

        // El código del trabajador será su usuario de acceso.
        $username = trim($trabajador->codigo);

        if (User::where('username', $username)->exists()) {
            throw ValidationException::withMessages([
                'trabajador_id' =>
                    'El código del trabajador ya está siendo utilizado como usuario.',
            ]);
        }

        $nombre = trim(implode(' ', array_filter([
            $trabajador->primer_nombre,
            $trabajador->segundo_nombre,
            $trabajador->primer_apellido,
            $trabajador->segundo_apellido,
        ])));

        User::create([
            'name' => $nombre,
            'username' => $username,

            // Se conserva email porque el esquema actual lo utiliza.
            // No se mostrará como usuario de acceso.
            'email' => 'trabajador-' . $trabajador->id
                . '@cuentas.local',

            'password' => Hash::make('12345678'),
            'trabajador_id' => $trabajador->id,
            'debe_cambiar_password' => true,
        ]);

        $usuario = User::where('username', $username)->firstOrFail();

        $usuario->assignRole('Colaborador');
    });

    return redirect()
        ->route('cuentas-trabajadores.index')
        ->with(
            'success',
            'La cuenta se creó correctamente. El usuario es el código del trabajador y la contraseña inicial es 12345678.'
        );
}

}
