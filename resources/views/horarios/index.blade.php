@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-clock text-primary"></i>
        Gestión de Horarios
    </h1>
    <button type="button"
        class="btn btn-primary"
        data-toggle="modal"
        data-target="#modalHorario">
        <i class="fas fa-plus-circle"></i>
        Nuevo Horario
    </button>
</div>

@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">
            &times;
        </button>
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Estadísticas -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $total }}</h3>
                <p>Total Horarios</p>
            </div>

            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $activos }}</h3>
                <p>Activos</p>
            </div>

            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $inactivos }}</h3>
                <p>Inactivos</p>
            </div>

            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card">
    <div class="card-header bg-light">
        <strong>
            <i class="fas fa-search"></i>
            Filtros de búsqueda
        </strong>
    </div>

    <div class="card-body">
        <form method="GET">
            <div class="row">
                <div class="col-md-4">
                    <label>Empresa</label>
                    <select name="empresa" class="form-control">
                        <option value="">Todas</option>
                        @foreach($empresas as $empresa)
                        <option
                            value="{{ $empresa->id }}"
                            {{ request('empresa')==$empresa->id ? 'selected':'' }}>
                            {{ $empresa->nombre_comercial }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Buscar</label>
                    <input type="text" name="buscar" class="form-control"
                        placeholder="Nombre del horario..." value="{{ request('buscar') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary mr-2"><i class="fas fa-search"></i> Buscar</button>

                    <a href="{{ route('horarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Limpiar </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>
            <i class="fas fa-list"></i>
            Listado de Horarios
        </strong>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="thead-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Empresa</th>
                    <th>Turno</th>
                    <th>Tipo</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Tolerancia</th>
                    <th>Estado</th>
                    <th width="180">Acciones</th>
                </tr>
            </thead>
            <tbody>

                @forelse($horarios as $horario)

                <tr>
                    <td class="text-center">
                        {{ $horario->id }}
                    </td>

                    <td>
                        {{ $horario->empresa->nombre_comercial }}
                    </td>

                    <td>
                        {{ $horario->nombre }}
                    </td>

                    <td class="text-center">
                        @if($horario->tipo_turno=='CONTINUO')
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i>
                                CONTINUO
                            </span>

                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-random"></i>
                                PARTIDO
                            </span>
                        @endif
                    </td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($horario->hora_ingreso)->format('H:i') }}

                        @if($horario->hora_ingreso2)
                            <br>
                            <small class="text-primary">
                                {{ \Carbon\Carbon::parse($horario->hora_ingreso2)->format('H:i') }}
                            </small>
                        @endif
                    </td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}
                        
                        @if($horario->hora_salida2)
                            <br>
                            <small class="text-primary">
                                {{ \Carbon\Carbon::parse($horario->hora_salida2)->format('H:i') }}
                            </small>
                        @endif
                    </td>

                    <td class="text-center">
                        {{ $horario->tolerancia }} min.
                    </td>

                    <td class="text-center">
                        @if($horario->estado)
                            <span class="badge badge-success">
                                Activo
                            </span>

                        @else
                            <span class="badge badge-danger">
                                Inactivo
                            </span>
                        @endif
                    </td>

                    <td class="text-center">
                        <a href="{{ route('horarios.show',$horario) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('horarios.edit',$horario) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('horarios.destroy', $horario->id) }}"
                            method="POST"
                            class="formulario-eliminar d-inline">

                            @csrf
                            @method('DELETE')

                            @if($horario->estado)
                                <button type="submit" class="btn btn-danger btn-sm" title="Desactivar">
                                    <i class="fas fa-toggle-off"></i>
                                </button>
                            @else
                                <button type="submit" class="btn btn-success btn-sm" title="Activar">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>

                @empty

                <tr>
                    <td
                        colspan="9"
                        class="text-center text-muted">
                        No existen horarios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $horarios->links() }}
    </div>
</div>

<!-- Modal Nuevo Horario -->
<div class="modal fade" id="modalHorario" tabindex="-1" role="dialog"
        aria-labelledby="modalHorarioLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalHorarioLabel">
                    <i class="fas fa-clock mr-2"></i>
                    Registrar nuevo horario
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Formulario -->
            <form action="{{ route('horarios.store') }}" method="POST" id="formHorario">
                @csrf

                <div class="modal-body">
                    <!-- Información general -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                Información del horario
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <!-- Empresa -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="empresa_id">
                                            Empresa
                                            <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-building"></i>
                                                </span>
                                            </div>

                                            <select name="empresa_id" id="empresa_id" class="form-control" required>
                                                @foreach($empresas as $empresa)
                                                    <option value="{{ $empresa->id }}">
                                                        {{ $empresa->nombre_comercial }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">
                                            Nombre del horario
                                            <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-tag"></i>
                                                </span>
                                            </div>

                                            <input type="text" name="nombre" id="nombre" class="form-control"
                                                placeholder="Ej. Turno Mañana" maxlength="100" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo de turno -->
                            <div class="form-group">
                                <label> Tipo de turno
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="tipo_turno"
                                                    id="turnoContinuo" value="CONTINUO"checked>

                                            <label class="custom-control-label" for="turnoContinuo">
                                                <i class="fas fa-check-circle text-success mr-1"></i>
                                                Turno continuo
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="tipo_turno"
                                                    id="turnoPartido" value="PARTIDO">

                                            <label class="custom-control-label" for="turnoPartido">
                                                <i class="fas fa-random text-warning mr-1"></i>
                                                Turno partido
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Horario -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-business-time mr-1"></i>
                                Jornada laboral
                            </h3>
                        </div>

                        <div class="card-body">
                            <!-- Primer turno -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hora_ingreso">
                                            Hora de ingreso
                                            <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-sign-in-alt text-success"></i>
                                                </span>
                                            </div>

                                            <input type="time" name="hora_ingreso" id="hora_ingreso" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hora_salida">
                                            Hora de salida
                                            <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-sign-out-alt text-danger"></i>
                                                </span>
                                            </div>

                                            <input type="time" name="hora_salida" id="hora_salida" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Segundo turno -->
                            <div id="segundoTurno"
                                style="display:none;">
                                <hr>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Configure el segundo turno.
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hora_ingreso2">
                                                Segundo ingreso
                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-sign-in-alt text-success"></i>
                                                    </span>
                                                </div>

                                                <input type="time" name="hora_ingreso2" id="hora_ingreso2" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hora_salida2">
                                                Segunda salida
                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-sign-out-alt text-danger"></i>
                                                    </span>
                                                </div>
                                                <input type="time" name="hora_salida2" id="hora_salida2" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog mr-1"></i>
                                Configuración
                            </h3>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="refrigerio">
                                    Refrigerio (minutos)
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-utensils"></i>
                                        </span>
                                    </div>

                                    <input type="number" name="refrigerio" id="refrigerio" class="form-control"
                                        value="0" min="0" required>

                                    <div class="input-group-append">
                                        <span class="input-group-text">minutos</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="card-body">
                                <div class="row">
                                    <!-- Tolerancia -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tolerancia">
                                                Tolerancia de ingreso
                                                <span class="text-danger">*</span>

                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-stopwatch"></i>
                                                    </span>
                                                </div>

                                                <input type="number" name="tolerancia" id="tolerancia" class="form-control"
                                                    value="10" min="0"max="120" required>

                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        minutos
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado">
                                                Estado
                                            </label>
                                            <div class="custom-control custom-switch mt-2">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="estado"
                                                    name="estado"
                                                    value="1"
                                                    checked>
                                                <label class="custom-control-label"
                                                    for="estado">
                                                    Horario activo
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                <!-- Observación -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observacion">
                                            Observación
                                            <small class="text-muted">(Opcional)</small>
                                        </label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-comment-alt"></i>
                                                </span>
                                            </div>

                                            <textarea
                                                name="observacion"
                                                id="observacion"
                                                class="form-control"
                                                rows="1"
                                                maxlength="100"
                                                placeholder="Ingrese alguna observación si es necesario..."
                                            ></textarea>
                                        </div>

                                        <small class="form-text text-muted">
                                            Máximo 100 caracteres.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Guardar horario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('css')
<style>
    #modalHorario input[type="text"],
    #modalHorario textarea {
        text-transform: uppercase;
    }
</style>
@endpush


@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('js')
<script>

$('.formulario-eliminar').submit(function(e){

    e.preventDefault();
    let form=this;

    Swal.fire({

        title:'¿Está seguro?',
        text:'Se cambiará el estado del horario.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#3085d6',
        cancelButtonColor:'#d33',
        confirmButtonText:'Sí, continuar',
        cancelButtonText:'Cancelar'

    }).then((result)=>{
        if(result.isConfirmed){
            form.submit();
        }
    });
});

</script>

<script>
    $(document).ready(function () {
    $('input[name="tipo_turno"]').on('change', function () {
        if ($('#turnoPartido').is(':checked')) {
            $('#segundoTurno').slideDown(250);
            $('#hora_ingreso2').prop('required', true);
            $('#hora_salida2').prop('required', true);
        } else {
            $('#segundoTurno').slideUp(250);
            $('#hora_ingreso2').prop('required', false).val('');
            $('#hora_salida2').prop('required', false).val('');
        }
    });
});
</script>


@endpush

@if(session('success'))
@push('js')
<script>

    Swal.fire({
        icon:'success',
        title:'Correcto',
        text:'{{ session('success') }}',
        timer:2500,
        showConfirmButton:false
    });
</script>

@endpush
@endif


@if(session('error'))
@push('js')

<script>

Swal.fire({
    icon:'error',
    title:'Error', 
    text:'{{ session('error') }}'
});

</script>
@endpush

@endif
