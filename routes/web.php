<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AsignacionHorarioController;
use App\Http\Controllers\AsignacionTurnoController;
use App\Http\Controllers\SucursalController;

/*
|--------------------------------------------------------------------------
| Página inicial
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Rutas específicas de trabajadores
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::get(
        '/trabajadores/validar-dni/{dni}',
        [TrabajadorController::class, 'validarDni']
    )->name('trabajadores.validarDni');

    Route::get(
        '/trabajadores/generar-codigo',
        [TrabajadorController::class, 'generarCodigo']
    )->name('trabajadores.generarCodigo');

    Route::get(
        '/empresa/{empresa}/areas',
        [TrabajadorController::class, 'areas']
    );

    Route::get(
        '/empresa/{empresa}/cargos',
        [TrabajadorController::class, 'cargos']
    );

    Route::resource('trabajadores', TrabajadorController::class)
        ->parameters([
            'trabajadores' => 'trabajador',
        ]);
});

/* Empresas */

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::get(
        '/empresas/validar-ruc/{ruc}',
        [EmpresaController::class, 'validarRuc']
    )->name('empresas.validarRuc');

    Route::resource('empresas', EmpresaController::class);
});

/* Áreas */

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::get('/areas/validar', [AreaController::class, 'validarNombre'])
        ->name('areas.validar');

    Route::resource('areas', AreaController::class);

});

/* Cargos */

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::get('/cargos/validar', [CargoController::class, 'validarNombre'])
        ->name('cargos.validar');

    Route::resource('cargos', CargoController::class);
});

/* Horarios */

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::get('/horarios/validar', [HorarioController::class, 'validarNombre'])
        ->name('horarios.validar');

    Route::resource('horarios', HorarioController::class);
});

/* Asignación de horarios */

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::resource('asignacion-horarios', AsignacionHorarioController::class)
        ->parameters([
            'asignacion-horarios' => 'asignacionHorario',
        ]);

});

/* Sucursales */

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::resource('sucursales', SucursalController::class)
        ->parameters([
            'sucursales' => 'sucursal',
        ]);
});

/* Asignación de turnos 

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::resource('asignacion-turnos', AsignacionTurnoController::class)
        ->parameters([
            'asignacion-turnos' => 'asignacionTurno',
        ]);
});*/


Route::middleware(['auth', 'role:Administrador'])->group(function () {

    Route::get(
        '/asignacion-turnos', [AsignacionTurnoController::class, 'index']
    )->name('asignacion-turnos.index');

    Route::post(
        '/asignacion-turnos', [AsignacionTurnoController::class, 'store']
    )->name('asignacion-turnos.store');

    Route::get(
        '/asignacion-turnos/consulta', [AsignacionTurnoController::class, 'consulta']
    )->name('asignacion-turnos.consulta');
    
    Route::get(
        '/asignacion-turnos/consulta/pdf',
        [AsignacionTurnoController::class, 'exportarPdf']
    )->name('asignacion-turnos.consulta.pdf');

    Route::get(
        '/asignacion-turnos/consulta/excel',
        [AsignacionTurnoController::class, 'exportarExcel']
    )->name('asignacion-turnos.consulta.excel');

});


require __DIR__ . '/auth.php';
