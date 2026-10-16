<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PrimerCambioPasswordController extends Controller
{
    public function edit()
    {
        return view('auth.primer-cambio-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_actual' => [
                'required',
                'string',
                'current_password',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8),
            ],
        ], [
            'password_actual.current_password' =>
                'La contraseña actual es incorrecta.',
            'password.confirmed' =>
                'Las contraseñas nuevas no coinciden.',
        ]);

        $usuario = $request->user();

        if (
            Hash::check(
                $request->input('password'),
                $usuario->password
            )
        ) {
            throw ValidationException::withMessages([
                'password' =>
                    'La nueva contraseña debe ser diferente de la actual.',
            ]);
        }

        $usuario->password = $request->input('password');
        $usuario->debe_cambiar_password = false;
        $usuario->save();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Tu contraseña se actualizó correctamente.'
            );
    }
}
