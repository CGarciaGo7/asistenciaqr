@extends('adminlte::page')

@section('title', 'Sucursales')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>
        <i class="fas fa-building text-primary"></i>
        Sucursales
    </h1>

    <button type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#modalNuevaSucursal">
        <i class="fas fa-plus"></i>
        Nueva Sucursal
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
                <p>Total Sucursales</p>
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
                <p>Sucursales Activas</p>
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
                <p>Sucursales Inactivas</p>
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
                        placeholder="Buscar por Nombre Sucursal">
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
                    <th>Nombre Sucursal</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th width="180">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sucursales as $sucursal)
                    <tr>
                        <td>
                            {{ $sucursal->nombre }}
                        </td>
                        <td>
                            {{ $sucursal->direccion ?: 'No registrada' }}
                        </td>
                        <td>
                            {{ $sucursal->telefono ?: 'No registrado' }}
                        </td>
                        <td>
                            {{ $sucursal->correo ?: 'No registrado' }}
                        </td>
                        <td>

                            @if($sucursal->estado)

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
                                    data-target="#modalVerSucursal{{ $sucursal->id }}"
                                    title="Ver sucursal">
                                <i class="fas fa-eye"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalEditarSucursal{{ $sucursal->id }}"
                                    title="Editar sucursal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('sucursales.destroy', $sucursal) }}"
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
        {{ $sucursales->links() }}
    </div>
</div>


{{-- MODAL NUEVA SUCURSAL --}}

<div class="modal fade"
    id="modalNuevaSucursal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalNuevaSucursalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg"
        role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"
                    id="modalNuevaSucursalLabel">
                    <i class="fas fa-building"></i>
                    Registrar Nueva Sucursal
                </h5>
                <button type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('sucursales.store') }}"
                method="POST">
                @csrf

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">
                                    Nombre de la Sucursal
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="nombre"
                                    id="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre') }}"
                                    placeholder="Ej. SOLIDARIA CASTILLA"
                                    oninput="this.value = this.value.toUpperCase()"
                                    required>
                                @error('nombre')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">
                                    Teléfono
                                </label>
                                <input type="text"
                                    name="telefono"
                                    id="telefono"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono') }}"
                                    maxlength="9"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 9)">

                                @error('telefono')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="direccion">
                            Dirección
                        </label>

                        <input type="text"
                            name="direccion"
                            id="direccion"
                            class="form-control @error('direccion') is-invalid @enderror"
                            value="{{ old('direccion') }}"
                            placeholder="Ingrese la dirección"
                            oninput="this.value = this.value.toUpperCase()">

                        @error('direccion')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="correo">
                            Correo electrónico
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    solidaria.
                                </span>
                            </div>
                            <input type="text"
                                name="correo"
                                id="correo"
                                class="form-control @error('correo') is-invalid @enderror"
                                value="{{ old('correo') }}"
                                placeholder="castilla"
                                autocomplete="off"
                                oninput="this.value = this.value.toLowerCase().replace(/\s/g, '')"
                                onkeydown="return event.key !== ' '">

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    @farmaciassolidaria.com
                                </span>
                            </div>
                        </div>

                        <small class="form-text text-muted">
                            Ingrese únicamente el identificador del correo sin espacios.
                            Ejemplo: <strong>castilla</strong>
                        </small>

                        @error('correo')
                            <span class="text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_horario">
                            Horario de atención
                            <span class="text-danger">*</span>
                        </label>

                        <select name="tipo_horario"
                                id="tipo_horario"
                                class="form-control @error('tipo_horario') is-invalid @enderror"
                                required>

                            <option value="07_23"
                                {{ old('tipo_horario', '07_23') == '07_23' ? 'selected' : '' }}>
                                07:00 a. m. - 11:00 p. m.
                            </option>

                            <option value="07_22"
                                {{ old('tipo_horario') == '07_22' ? 'selected' : '' }}>
                                07:00 a. m. - 10:00 p. m.
                            </option>

                            <option value="08_20"
                                {{ old('tipo_horario') == '08_20' ? 'selected' : '' }}>
                                08:00 a. m. - 08:00 p. m. (Especial)
                            </option>
                        </select>

                        @error('tipo_horario')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror
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
                        Registrar Sucursal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODALES VER Y EDITAR --}}

@foreach($sucursales as $sucursal)
    {{-- MODAL VER SUCURSAL --}}

    <div class="modal fade"
        id="modalVerSucursal{{ $sucursal->id }}"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalVerSucursalLabel{{ $sucursal->id }}"
        aria-hidden="true">

        <div class="modal-dialog modal-lg"
            role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"
                        id="modalVerSucursalLabel{{ $sucursal->id }}">
                        <i class="fas fa-building"></i>
                        Detalle de la Sucursal
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
                                <i class="fas fa-building text-info"></i>
                                Nombre de la Sucursal
                            </label>

                            <div class="form-control bg-light">
                                {{ $sucursal->nombre }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">
                                <i class="fas fa-toggle-on text-info"></i>
                                Estado
                            </label>
                            <div>
                                @if($sucursal->estado)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i>
                                        ACTIVA
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle"></i>
                                        INACTIVA
                                    </span>
                                @endif

                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">
                                <i class="fas fa-map-marker-alt text-info"></i>
                                Dirección
                            </label>
                            <div class="form-control bg-light">
                                {{ $sucursal->direccion ?: 'No registrada' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">
                                <i class="fas fa-phone text-info"></i>
                                Teléfono
                            </label>

                            <div class="form-control bg-light">
                                {{ $sucursal->telefono ?: 'No registrado' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">
                                <i class="fas fa-envelope text-info"></i>
                                Correo electrónico
                            </label>

                            <div class="form-control bg-light">
                                {{ $sucursal->correo ?: 'No registrado' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">
                                <i class="fas fa-envelope text-info"></i>
                                Horario de atención
                            </label>

                            <div class="form-control bg-light">
                                {{ $sucursal->tipo_horario ?: 'No registrado' }}
                            </div>
                        </div>
                    </div>
                </div>

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

{{-- MODAL EDITAR SUCURSAL --}}

    <div class="modal fade"
        id="modalEditarSucursal{{ $sucursal->id }}"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalEditarSucursalLabel{{ $sucursal->id }}"
        aria-hidden="true">

        <div class="modal-dialog modal-lg"
            role="document">
            <div class="modal-content">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title"
                        id="modalEditarSucursalLabel{{ $sucursal->id }}">
                        <i class="fas fa-edit"></i>
                        Editar Sucursal
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('sucursales.update', $sucursal) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_{{ $sucursal->id }}">
                                        Nombre de la Sucursal
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        name="nombre"
                                        id="nombre_{{ $sucursal->id }}"
                                        class="form-control @if(session('editar_sucursal_id') == $sucursal->id && $errors->has('nombre')) is-invalid @endif"
                                        value="{{ session('editar_sucursal_id') == $sucursal->id ? old('nombre', $sucursal->nombre) : $sucursal->nombre }}"
                                        oninput="this.value = this.value.toUpperCase()"
                                        required>

                                    @if(session('editar_sucursal_id') == $sucursal->id)
                                        @error('nombre')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono_{{ $sucursal->id }}">
                                        Teléfono
                                    </label>
                                    <input type="text"
                                        name="telefono"
                                        id="telefono_{{ $sucursal->id }}"
                                        class="form-control @if(session('editar_sucursal_id') == $sucursal->id && $errors->has('telefono')) is-invalid @endif"
                                        value="{{ session('editar_sucursal_id') == $sucursal->id ? old('telefono', $sucursal->telefono) : $sucursal->telefono }}"
                                        maxlength="9"
                                        inputmode="numeric"
                                        oninput="this.value = this.value.replace(/\D/g, '').slice(0, 9)">

                                    @if(session('editar_sucursal_id') == $sucursal->id)
                                        @error('telefono')
                                            <span class="invalid-feedback">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="direccion_{{ $sucursal->id }}">
                                Dirección
                            </label>

                            <input type="text"
                                name="direccion"
                                id="direccion_{{ $sucursal->id }}"
                                class="form-control @if(session('editar_sucursal_id') == $sucursal->id && $errors->has('direccion')) is-invalid @endif"
                                value="{{ session('editar_sucursal_id') == $sucursal->id ? old('direccion', $sucursal->direccion) : $sucursal->direccion }}"
                                oninput="this.value = this.value.toUpperCase()">

                            @if(session('editar_sucursal_id') == $sucursal->id)
                                @error('direccion')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="correo_{{ $sucursal->id }}">
                                Correo electrónico
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        solidaria.
                                    </span>
                                </div>
                                <input type="text"
                                    name="correo"
                                    id="correo_{{ $sucursal->id }}"
                                    class="form-control @if(session('editar_sucursal_id') == $sucursal->id && $errors->has('correo')) is-invalid @endif"
                                    value="{{ session('editar_sucursal_id') == $sucursal->id
                                            ? old('correo', $sucursal->correo ? preg_replace('/^solidaria\.(.*)@farmaciassolidaria\.com$/', '$1', $sucursal->correo) : '')
                                            : ($sucursal->correo ? preg_replace('/^solidaria\.(.*)@farmaciassolidaria\.com$/', '$1', $sucursal->correo) : '') }}"
                                    autocomplete="off"
                                    oninput="this.value = this.value.toLowerCase().replace(/\s/g, '')"
                                    onkeydown="return event.key !== ' '">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        @farmaciassolidaria.com
                                    </span>
                                </div>
                            </div>

                            <small class="form-text text-muted">
                                Ingrese únicamente el identificador del correo sin espacios.
                            </small>


                            @if(session('editar_sucursal_id') == $sucursal->id)
                                @error('correo')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_horario_{{ $sucursal->id }}">
                                <i class="fas fa-clock text-warning"></i>
                                Horario de atención
                                <span class="text-danger">*</span>
                            </label>

                            <select name="tipo_horario"
                                    id="tipo_horario_{{ $sucursal->id }}"
                                    class="form-control @if(session('editar_sucursal_id') == $sucursal->id && $errors->has('tipo_horario')) is-invalid @endif"
                                    required>

                                <option value="07_23"
                                    {{ old('tipo_horario', $sucursal->tipo_horario ?? '07_23') == '07_23' ? 'selected' : '' }}>
                                    07:00 a. m. - 11:00 p. m.
                                </option>

                                <option value="07_22"
                                    {{ old('tipo_horario', $sucursal->tipo_horario ?? '07_23') == '07_22' ? 'selected' : '' }}>
                                    07:00 a. m. - 10:00 p. m.
                                </option>

                                <option value="08_20"
                                    {{ old('tipo_horario', $sucursal->tipo_horario ?? '07_23') == '08_20' ? 'selected' : '' }}>
                                    08:00 a. m. - 08:00 p. m. (Especial)
                                </option>

                            </select>

                            @if(session('editar_sucursal_id') == $sucursal->id)
                                @error('tipo_horario')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @endif

                            <small class="form-text text-muted">
                                Seleccione el horario habitual de atención de esta sucursal.
                            </small>
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
        timer: 2500,
        timerProgressBar: true
    });
});
</script>

@endif

<script>

$(document).ready(function () {

    @if (session('operacion_sucursal') === 'crear')

        $('#modalNuevaSucursal').modal('show');

    @elseif (
        session('operacion_sucursal') === 'editar'
        && session('editar_sucursal_id')
    )

        $('#modalEditarSucursal{{ session('editar_sucursal_id') }}').modal('show');

    @endif

});

</script>

<script>
$(document).ready(function () {

    $('.formEliminar').on('submit', function (e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: '¿Cambiar estado de la sucursal?',
            text: 'La sucursal será activada o desactivada.',
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


    // Modal Nueva Sucursal

    $('#modalNuevaSucursal').on('shown.bs.modal', function () {

        $(this)
            .find('input[name="nombre"]')
            .trigger('focus');

    });


    // Modales Editar Sucursal

    $('[id^="modalEditarSucursal"]').on('shown.bs.modal', function () {

        $(this)
            .find('input[name="nombre"]')
            .trigger('focus');

    });

});

</script>

@stop