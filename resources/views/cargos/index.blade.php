@extends('adminlte::page')

@section('title', 'Cargos')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>
        <i class="fas fa-sitemap text-primary"></i>
        Cargos
    </h1>

    <button type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#modalNuevoCargo">
        <i class="fas fa-plus"></i>
        Crear Nuevo
    </button>
</div>

@stop

@section('content')

{{-- Cards --}}

<div class="row">
    <div class="col-md-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $total }}</h3>
                <p>Total Cargos</p>
            </div>

            <div class="icon">
                <i class="fas fa-sitemap"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $activas }}</h3>
                <p>Cargos Activos</p>
            </div>

            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $inactivas }}</h3>
                <p>Cargos Inactivos</p>
            </div>

            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>


<div class="card">

    {{-- Buscador --}}
    <div class="card-header">
        <form method="GET">
            <div class="row">
                <div class="col-md-10">
                    <input type="text"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        class="form-control"
                        placeholder="Buscar por Nombre Cargo">
                </div>
                <div class="col-md-2">
                    <button type="submit"
                            class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                        Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLA --}}
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nombre Cargo</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th width="180">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($cargos as $cargo)
                    <tr>
                        <td>
                            {{ $cargo->nombre }}
                        </td>
                        <td>
                            {{ $cargo->descripcion ?: 'No registrada' }}
                        </td>
                        
                        <td>
                            @if($cargo->estado)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i>
                                    Activo
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-ban"></i>
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-info btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalVerCargo{{ $cargo->id }}"
                                    title="Ver Cargo">
                                <i class="fas fa-eye"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalEditarCargo{{ $cargo->id }}"
                                    title="Editar Cargo">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('cargos.destroy', $cargo) }}"
                                method="POST"
                                class="formEliminar d-inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        title="Cambiar estado">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty

                    <tr>
                        <td colspan="6"
                            class="text-center">
                            No existen registros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="card-footer">
        {{ $cargos->links() }}
    </div>
</div>


{{-- MODAL NUEVO CARGO --}}

<div class="modal fade"
    id="modalNuevoCargo"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalNuevoCargoLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg"
        role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"
                    id="modalNuevoCargoLabel">
                    <i class="fas fa-sitemap"></i>
                    Registrar Nuevo Cargo
                </h5>
                <button type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('cargos.store') }}"
                method="POST">
                @csrf

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="nombre">
                                    Nombre del Cargo
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="nombre"
                                    id="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre') }}"
                                    oninput="this.value = this.value.toUpperCase()"
                                    required>
                                @error('nombre')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                            <div class="col-md-7">        
                                <div class="form-group">
                                    <label for="descripcion">
                                        Descripcion
                                    </label>

                                    <input type="text"
                                        name="descripcion"
                                        id="descripcion"
                                        class="form-control @error('descripcion') is-invalid @enderror"
                                        value="{{ old('descripcion') }}"
                                        oninput="this.value = this.value.toUpperCase()">

                                    @error('descripcion')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                        </div>
                    </div>    
                </div>    
                
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Cargo
                    </button>
                    
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODALES VER Y EDITAR --}}

@foreach($cargos as $cargo)
    {{-- MODAL VER CARGO --}}

    <div class="modal fade"
    id="modalVerCargo{{ $cargo->id }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalVerCargoLabel{{ $cargo->id }}"
    aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"
                    id="modalVerCargoLabel{{ $cargo->id }}">
                    <i class="fas fa-briefcase"></i>
                    Detalle del Cargo
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">
                            <i class="fas fa-briefcase text-info"></i>
                            Nombre del Cargo
                        </label>

                        <div class="form-control bg-light">
                            {{ $cargo->nombre }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">
                            <i class="fas fa-toggle-on text-info"></i>
                            Estado
                        </label>
                        <div>
                            @if($cargo->estado)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i>
                                    ACTIVO
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i>
                                    INACTIVO
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="font-weight-bold">
                            <i class="fas fa-align-left text-info"></i>
                            Descripción
                        </label>
                        <div class="form-control bg-light">
                            {{ $cargo->descripcion ?: 'No registrado' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>


    {{-- MODAL EDITAR CARGO --}}

    <div class="modal fade"
        id="modalEditarCargo{{ $cargo->id }}"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalEditarCargoLabel{{ $cargo->id }}"
        aria-hidden="true">

        <div class="modal-dialog modal-lg"
            role="document">
            <div class="modal-content">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title"
                        id="modalEditarCargoLabel{{ $cargo->id }}">
                        <i class="fas fa-edit"></i>
                        Editar Cargo
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('cargos.update', $cargo) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_{{ $cargo->id }}">
                                        Nombre del Cargo
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        name="nombre"
                                        id="nombre_{{ $cargo->id }}"
                                        class="form-control @if(session('editar_cargo_id') == $cargo->id && $errors->has('nombre')) is-invalid @endif"
                                        value="{{ session('editar_cargo_id') == $cargo->id ? old('nombre', $cargo->nombre) : $cargo->nombre }}"
                                        oninput="this.value = this.value.toUpperCase()"
                                        required>

                                    @if(session('editar_cargo_id') == $cargo->id)
                                        @error('nombre')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion{{ $cargo->id }}">
                                Descripcion
                            </label>

                            <input type="text"
                                name="descripcion"
                                id="descripcion_{{ $cargo->id }}"
                                class="form-control @if(session('editar_cargo_id') == $cargo->id && $errors->has('descripcion')) is-invalid @endif"
                                value="{{ session('editar_cargo_id') == $cargo->id ? old('cargo', $cargo->descripcion) : $cargo->descripcion }}"
                                oninput="this.value = this.value.toUpperCase()">

                            @if(session('editar_cargo_id') == $cargo->id)
                                @error('descripcion')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>

                        <button type="submit"
                                class="btn btn-warning">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach

@stop

@section('js')

{{-- SWEETALERT SUCCESS --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@if(session('success'))

<script>

$(document).ready(function () {

    Swal.fire({

        icon: 'success',
        title: 'Operación Exitosa',
        text: @json(session('success')),
        confirmButtonText: 'Aceptar',
        timer: 2000,
        timerProgressBar: true
    });
});
</script>

@endif

<script>

$(document).ready(function () {

    @if (session('operacion_cargo') === 'crear')

        $('#modalNuevoCargo').modal('show');

    @elseif (
        session('operacion_cargo') === 'editar'
        && session('editar_cargo_id')
    )

        $('#modalEditarCargo{{ session('editar_cargo_id') }}').modal('show');

    @endif

});

</script>

<script>
$(document).ready(function () {

    $('.formEliminar').on('submit', function (e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: '¿Cambiar estado del Cargo?',
            text: 'Cargo será activado o desactivado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

</script>

{{-- FOCUS DE LOS MODALES --}}
<script>

$(document).ready(function () {

   // Modal Nuevo Cargo

    $('#modalNuevoCargo').on('shown.bs.modal', function () {

        $(this)
            .find('input[name="nombre"]')
            .trigger('focus');

    });


    // Modales Editar Cargo

    $('[id^="modalEditarCargo"]').on('shown.bs.modal', function () {

        $(this)
            .find('input[name="nombre"]')
            .trigger('focus');

    });

});

</script>

@stop