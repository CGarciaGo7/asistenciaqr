@extends('adminlte::page')

@section('title', 'Cuentas de trabajadores')

@section('content_header')
    <h1>
        <i class="fas fa-user-shield mr-2"></i>
        Cuentas de trabajadores
    </h1>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle mr-1"></i>
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>No se pudo crear la cuenta.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-plus mr-1"></i>
            Registrar acceso para un trabajador
        </h3>
    </div>

    <form method="POST" action="{{ route('cuentas-trabajadores.store') }}">
        @csrf

        <div class="card-body">
            <div class="form-group">
                <label for="trabajador_id">Trabajador</label>

                <select name="trabajador_id"
                        id="trabajador_id"
                        class="form-control"
                        required>
                    <option value="">Seleccione un trabajador</option>

                    @foreach($trabajadores as $trabajador)
                        @if(!$trabajador->user)
                            <option
                                value="{{ $trabajador->id }}"
                                data-username="{{ $trabajador->codigo }}"
                                @selected(old('trabajador_id') == $trabajador->id)>
                                {{ trim($trabajador->primer_nombre . ' ' .
                                    $trabajador->segundo_nombre . ' ' .
                                    $trabajador->primer_apellido . ' ' .
                                    $trabajador->segundo_apellido) }}
                            </option>
                        @endif
                    @endforeach
                </select>

                <small class="form-text text-muted">
                    Solo aparecen trabajadores sin cuenta de acceso.
                </small>
            </div>

            <div class="form-group">
                <label for="username">Usuario de acceso</label>

                <input type="text"
                       id="username"
                       class="form-control"
                       placeholder="Se generará al seleccionar al trabajador"
                       readonly>

                <small class="form-text text-muted">
                    Se utilizará el código del trabajador como usuario.
                </small>
            </div>

            <div class="form-group">
                <label for="password_inicial">Contraseña inicial</label>

                <div class="input-group">
                    <input type="text"
                           id="password_inicial"
                           class="form-control"
                           value="12345678"
                           readonly>

                    <div class="input-group-append">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                id="copiarPassword">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                </div>

                <small class="form-text text-muted">
                    El trabajador deberá cambiarla en su primer ingreso.
                </small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-info">
                <i class="fas fa-user-check mr-1"></i>
                Crear cuenta
            </button>
        </div>
    </form>
</div>


<div class="card">
    <div class="card-header">
        <h3 class="card-title">Trabajadores registrados</h3>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Trabajador</th>
                    <th>Sucursal</th>
                    <th>Usuario acceso</th>
                    <th>Estado de cuenta</th>
                </tr>
            </thead>

            <tbody>
                @foreach($trabajadores as $trabajador)
                    <tr>
                        <td>{{ $trabajador->codigo }}</td>
                        <td>
                            {{ $trabajador->primer_nombre }}
                            {{ $trabajador->segundo_nombre }}
                            {{ $trabajador->primer_apellido }}
                            {{ $trabajador->segundo_apellido }}
                        </td>
                        <td>
                            {{ $trabajador->sucursal->nombre ?? 'Sin sucursal' }}
                        </td>
                        <td>{{ $trabajador->user->username ?? '—' }}</td>
                        <td>
                            @if($trabajador->user)
                                <span class="badge badge-success">
                                    Cuenta creada
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    Sin cuenta
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop


@section('js')

<script>
$(function () {
    function actualizarUsuario() {
        const opcion = $('#trabajador_id option:selected');
        $('#username').val(opcion.data('username') || '');
    }

    $('#trabajador_id').on('change', actualizarUsuario);

    // Mantener el usuario visible si hay errores de validación.
    actualizarUsuario();

    $('#copiarPassword').on('click', async function () {
        try {
            await navigator.clipboard.writeText('12345678');
            $(this).html('<i class="fas fa-check"></i> Copiado');
        } catch (error) {
            alert('No se pudo copiar automáticamente. Usa la contraseña 12345678.');
        }
    });
});
</script>
@stop
