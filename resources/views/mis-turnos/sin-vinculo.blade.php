
@extends('adminlte::page')

@section('title', 'Mis turnos')

@section('content')
<div class="card card-outline card-warning mt-3">
    <div class="card-body text-center py-5">
        <i class="fas fa-user-lock fa-3x text-warning mb-3"></i>

        <h3>Cuenta sin trabajador vinculado</h3>

        <p class="text-muted">
            Tu cuenta todavía no está asociada a un trabajador.
            Comunícate con el administrador del sistema para completar
            la configuración.
        </p>

        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            Volver al inicio
        </a>
    </div>
</div>
@stop
