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
use App\Http\Controllers\MisTurnosController;
use App\Http\Controllers\CuentaTrabajadorController;
use App\Http\Controllers\PrimerCambioPasswordController;

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
| Cambio obligatorio de contraseña inicial
|--------------------------------------------------------------------------
| Estas rutas llevan únicamente el middleware auth.
| No deben llevar password.change.required, para evitar bucles.
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/cambiar-password-inicial', [PrimerCambioPasswordController::class,'edit'])
            ->name('password.first-change.edit');

    Route::put('/cambiar-password-inicial', [PrimerCambioPasswordController::class,'update'])
            ->name('password.first-change.update');
});

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
| El middleware password.change.required obliga a cambiar
| la contraseña antes de acceder a estas páginas.
*/

Route::middleware(['auth', 'password.change.required'])->group(function () {

    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class,'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class,'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class,'destroy'])->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| Rutas exclusivas del administrador
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:Administrador','password.change.required'])
        ->group(function () {

    /* Trabajadores */

    Route::get('/trabajadores/validar-dni/{dni}',[TrabajadorController::class, 'validarDni'])
        ->name('trabajadores.validarDni');

    Route::get('/trabajadores/generar-codigo',[TrabajadorController::class, 'generarCodigo'])
        ->name('trabajadores.generarCodigo');

    Route::get('/empresa/{empresa}/areas',[TrabajadorController::class, 'areas']);

    Route::get('/empresa/{empresa}/cargos',[TrabajadorController::class, 'cargos']);

    Route::resource('trabajadores', TrabajadorController::class)
        ->parameters(['trabajadores' => 'trabajador',]);

    /* Empresas  */

    Route::get(
        '/empresas/validar-ruc/{ruc}',[EmpresaController::class, 'validarRuc'])
            ->name('empresas.validarRuc');

    Route::resource('empresas', EmpresaController::class);

    /* Áreas */

    Route::get('/areas/validar',[AreaController::class, 'validarNombre'])
        ->name('areas.validar');

    Route::resource('areas', AreaController::class);

    /* Cargos */

    Route::get('/cargos/validar',[CargoController::class, 'validarNombre'])
            ->name('cargos.validar');

    Route::resource('cargos', CargoController::class);

    /* Horarios */

    Route::get('/horarios/validar',[HorarioController::class, 'validarNombre'])
        ->name('horarios.validar');

    Route::resource('horarios', HorarioController::class);

    /* Asignación de horarios */

    Route::resource('asignacion-horarios', AsignacionHorarioController::class)->parameters(['asignacion-horarios' => 'asignacionHorario',]);

    /* Sucursales */

    Route::resource('sucursales', SucursalController::class)
        ->parameters(['sucursales' => 'sucursal',]);

    /* Asignación y consulta de turnos */

    Route::get('/asignacion-turnos',[AsignacionTurnoController::class, 'index'])
        ->name('asignacion-turnos.index');

    Route::post('/asignacion-turnos',[AsignacionTurnoController::class, 'store'])
        ->name('asignacion-turnos.store');

    Route::get('/asignacion-turnos/consulta/pdf',[AsignacionTurnoController::class, 'exportarPdf'])
        ->name('asignacion-turnos.consulta.pdf');

    Route::get(
        '/asignacion-turnos/consulta/excel',[AsignacionTurnoController::class, 'exportarExcel'])
        ->name('asignacion-turnos.consulta.excel');

    /* Administración de cuentas de trabajadores */

    Route::get('/cuentas-trabajadores',[CuentaTrabajadorController::class, 'index'])
        ->name('cuentas-trabajadores.index');

    Route::post('/cuentas-trabajadores',[CuentaTrabajadorController::class, 'store'])
        ->name('cuentas-trabajadores.store');
});

/*
|--------------------------------------------------------------------------
| Turnos del trabajador
|--------------------------------------------------------------------------
| Permite que un usuario autenticado con rol Colaborador consulte
| sus propios turnos. El controlador debe limitar los datos al usuario.
*/

Route::middleware(['auth','role:Colaborador','password.change.required'])
        ->group(function () {

    Route::get('/mis-turnos',[MisTurnosController::class, 'index'])
        ->name('mis-turnos.index');
});

Route::middleware(['auth','role:Administrador|Colaborador', 'password.change.required',])
    ->group(function () {
    Route::get('/asignacion-turnos/consulta',[AsignacionTurnoController::class, 'consulta'])
        ->name('asignacion-turnos.consulta');
});


require __DIR__ . '/auth.php';