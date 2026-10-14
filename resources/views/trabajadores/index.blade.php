@extends('adminlte::page')

@section('title', 'Trabajadores')

@section('content_header')

<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            <i class="fas fa-users text-primary"></i>
            Gestión de Trabajadores
        </h3>
        <button type="button"
                class="btn btn-primary"
                data-toggle="modal"
                data-target="#modalTrabajador">

            <i class="fas fa-plus-circle"></i>
            Nuevo Trabajador
        </button>
    </div>
@stop

@section('css')
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet"
          href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

    <style>
        #tablaTrabajadores thead th {vertical-align: middle;
            white-space: nowrap;}

        #tablaTrabajadores tbody td {vertical-align: middle;}
            .dt-buttons .btn {margin-right: 4px;margin-bottom: 6px;}
            .dataTables_filter input {margin-left: 6px;}
            .dataTables_length select {margin: 0 5px;}

    /* BUSCADOR DATATABLE */
        #tablaTrabajadores_filter {
            width: 100%;
            margin-bottom: 15px;}

        #tablaTrabajadores_filter label {
            width: 100%;
            margin: 0; }

        #tablaTrabajadores_filter input {
            width: 100% !important;
            height: 42px;
            font-size: 15px;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            box-sizing: border-box;}
</style>
@endsection


@section('content')

{{-- CARDS --}}
    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>
                        {{ $totalTrabajadores }}
                    </h3>
                    <p>Total Trabajadores</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>
                        {{ $activos }}
                    </h3>
                    <p>Trabajadores Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>
                        {{ $inactivos }}
                    </h3>
                    <p>Trabajadores Inactivos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- BUSCADOR 
    <div class="card">
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-10">
                        <input
                            type="text"
                            name="buscar"
                            value="{{ $buscar }}"
                            class="form-control"
                            placeholder="Buscar por código, DNI o nombre">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}

    {{-- TABLA --}}

    <div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaTrabajadores"
                class="table table-bordered table-hover table-striped w-100">

                <thead class="thead-dark">
                    <tr>
                        <th>N.º</th>
                        <th style="text-align: center">Código</th>
                        <th style="text-align: center">Nombre del trabajador</th>
                        <th style="text-align: center">DNI</th>
                        <th style="text-align: center">Sucursal</th>
                        <th style="text-align: center">Área</th>
                        <th style="text-align: center">Cargo</th>
                        <th style="text-align: center">Estado</th>
                        <th style="min-width: 120px; text-align: center;">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($trabajadores as $trabajador)
                        <tr>
                            {{-- DataTables generará la numeración --}}
                            <td></td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $trabajador->codigo }}
                                </span>
                            </td>

                            <td>
                                <strong>
                                    {{ $trabajador->primer_apellido }}
                                    {{ $trabajador->segundo_apellido }}
                                </strong>
                                <br>
                                {{ $trabajador->primer_nombre }}
                                {{ $trabajador->segundo_nombre }}
                            </td>

                            <td>{{ $trabajador->dni }}</td>
                            <td>{{ $trabajador->sucursal->nombre ?? '-' }}</td>
                            <td>{{ $trabajador->area->nombre ?? '-' }}</td>
                            <td>{{ $trabajador->cargo->nombre ?? '-' }}</td>

                            <td>
                                @if($trabajador->estado)
                                    <span class="badge badge-success" style="text-align: center">Activo</span>
                                @else
                                    <span class="badge badge-danger" style="text-align: center">Inactivo</span>
                                @endif
                            </td>

                            <td class="text-nowrap" style="min-width: 120px; align-items: center; display: flex; justify-content: center;">
                                <button type="button"
                                        class="btn btn-info btn-sm btn-ver-trabajador"
                                        data-id="{{ $trabajador->id }}"
                                        title="Ver información">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button type="button"
                                        class="btn btn-warning btn-sm btn-editar-trabajador"
                                        data-id="{{ $trabajador->id }}"
                                        title="Editar trabajador">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>

{{-- MODAL REGISTRAR TRABAJADOR --}}

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Se encontraron errores:</strong>

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="modal fade"
    id="modalTrabajador"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTrabajadorLabel"
    aria-hidden="true">

   <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        role="document">
        <div class="modal-content">
            {{-- ENCABEZADO --}}
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title mb-1"
                        id="modalTrabajadorLabel">
                        <i class="fas fa-user-plus mr-2"></i>
                        Registrar trabajador
                    </h5>

                    <small class="d-block"
                        style="opacity: .9;">
                        Registrar un nuevo trabajador
                    </small>
                </div>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Cerrar">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>

            {{-- FORMULARIO --}}
            <form action="{{ route('trabajadores.store') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    {{-- DATOS LABORALES --}}
                    <div class="mb-3">
                        <h6 class="text-primary font-weight-bold">
                            <i class="fas fa-briefcase mr-1"></i>
                            Información laboral
                        </h6>
                        <hr>
                    </div>

                    <div class="row">
                        {{-- EMPRESA --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="empresa_id">
                                    Empresa
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-building"></i>
                                        </span>
                                    </div>
                                    <select
                                        id="empresa_id"
                                        class="form-control"
                                        required>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">
                                                {{ $empresa->razon_social }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input
                                        type="hidden"
                                        name="empresa_id"
                                        value="{{ $empresas->first()->id ?? '' }}">
                                </div>
                            </div>
                        </div>

                        {{-- SUCURSAL --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sucursal_id">
                                    Sucursal
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-store"></i>
                                        </span>
                                    </div>

                                    <select
                                        name="sucursal_id"
                                        id="sucursal_id"
                                        class="form-control"
                                        required>

                                        <option value="">
                                            Seleccione una sucursal
                                        </option>

                                        @foreach($sucursales as $sucursal)
                                            <option
                                                value="{{ $sucursal->id }}"
                                                data-empresa="{{ $sucursal->empresa->nombre }}">
                                                {{ $sucursal->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- ÁREA --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="area_id">
                                    Área
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-sitemap"></i>
                                        </span>
                                    </div>

                                    <select
                                        name="area_id"
                                        id="area_id"
                                        class="form-control"
                                        required>
                                        <option value="">
                                            Seleccione un área
                                        </option>

                                        @foreach($areas as $area)
                                            <option
                                                value="{{ $area->id }}"
                                                data-empresa="{{ $area->nombre }}">
                                                {{ $area->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- CARGO --}}
                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="cargo_id">
                                    Cargo
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-user-tie"></i>
                                        </span>
                                    </div>

                                    <select
                                        name="cargo_id"
                                        id="cargo_id"
                                        class="form-control"
                                        required>

                                        <option value="">
                                            Seleccione un cargo
                                        </option>

                                        @foreach($cargos as $cargo)
                                            <option
                                                value="{{ $cargo->id }}"
                                                data-empresa="{{ $cargo->nombre }}">
                                                {{ $cargo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DATOS PERSONALES --}}
                    <div class="mb-3 mt-2">
                        <h6 class="text-primary font-weight-bold">
                            <i class="fas fa-user mr-1"></i>
                            Información personal
                        </h6>
                        <hr>
                    </div>

                    <div class="row">
                    {{-- PRIMER NOMBRE --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="primer_nombre">
                                Primer nombre
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>

                                <input
                                    type="text"
                                    name="primer_nombre"
                                    id="primer_nombre"
                                    class="form-control"
                                    placeholder="Ingrese el primer nombre"
                                    required>
                            </div>
                        </div>
                    </div>

                    {{-- SEGUNDO NOMBRE --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="segundo_nombre">
                                Segundo nombre
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>

                                <input
                                    type="text"
                                    name="segundo_nombre"
                                    id="segundo_nombre"
                                    class="form-control"
                                    placeholder="Ingrese el segundo nombre">
                            </div>
                        </div>
                    </div>

                    {{-- PRIMER APELLIDO --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="primer_apellido">
                                Primer apellido
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                                <input
                                    type="text"
                                    name="primer_apellido"
                                    id="primer_apellido"
                                    class="form-control"
                                    placeholder="Ingrese el primer apellido"
                                    required>
                            </div>
                        </div>
                    </div>

                    {{-- SEGUNDO APELLIDO --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="segundo_apellido">
                                Segundo apellido
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>

                                <input
                                    type="text"
                                    name="segundo_apellido"
                                    id="segundo_apellido"
                                    class="form-control"
                                    placeholder="Ingrese el segundo apellido"
                                    required>
                            </div>
                        </div>
                    </div>
                        {{-- DNI --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dni">
                                DNI
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                </div>
                                <input type="text" name="dni" id="dni" class="form-control"
                                    maxlength="8"
                                    inputmode="numeric"
                                    placeholder="Ingrese el DNI"
                                    required>
                            </div>
                        </div>
                    </div>
                    
                        {{-- SEXO --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sexo">
                                    Sexo
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-venus-mars"></i>
                                        </span>
                                    </div>
                                    <select name="sexo" id="sexo" class="form-control" required>
                                        <option value="">
                                            Seleccione
                                        </option>
                                        <option value="M">
                                            Masculino
                                        </option>
                                        <option value="F">
                                            Femenino
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    {{-- TELEFONO --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">
                                    Teléfono
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="telefono" id="telefono" class="form-control" maxlength="20"
                                        placeholder="Ingrese el teléfono">
                                </div>
                            </div>
                        </div>

                        {{-- FECHA INGRESO --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_ingreso">
                                    Fecha de ingreso
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar-plus"></i>
                                        </span>
                                    </div>
                                    <input
                                        type="date"
                                        name="fecha_ingreso"
                                        id="fecha_ingreso"
                                        class="form-control"
                                        value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                        
                        {{-- DIRECCIÓN --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="direccion">
                                    Dirección
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="direccion" id="direccion" class="form-control"
                                        placeholder="Ingrese la dirección">
                                </div>
                            </div>
                        </div>
                            
                        {{-- FOTO --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="foto">
                                    Fotografía
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-camera"></i>
                                        </span>
                                    </div>
                                    <div class="custom-file">

                                        <input
                                            type="file"
                                            name="foto"
                                            id="foto"
                                            class="custom-file-input"
                                            accept=".jpg,.jpeg,.png">
                                        <label
                                            class="custom-file-label"
                                            for="foto">
                                            Seleccionar fotografía
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CÓDIGO --}}
                    <div class="col-md-6">
                        <div class="form-group" hidden>
                            <label for="codigo">
                                Código
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-badge"></i>
                                    </span>
                                </div>

                                <input
                                    type="text"
                                    name="codigo"
                                    id="codigo"
                                    class="form-control font-weight-bold"
                                    placeholder="Código autogenerado"
                                    readonly>
                            </div>

                            <small class="form-text text-muted">
                                El código será generado automáticamente.
                            </small>
                        </div>
                    </div>

                    </div>
                </div>
                {{-- FOOTER DEL MODAL --}}
                <div class="modal-footer bg-light">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar trabajador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL VER TRABAJADOR --}}

<div class="modal fade"
    id="modalVerTrabajador"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalVerTrabajadorLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered"
        role="document">
        <div class="modal-content">
            {{-- ENCABEZADO --}}
            <div class="modal-header bg-info text-white">
                <div>
                    <h5 class="modal-title"
                        id="modalVerTrabajadorLabel">
                        <i class="fas fa-user mr-2"></i>
                        Información del trabajador
                    </h5>
                    <small>
                        Detalle de la información registrada
                    </small>
                </div>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Cerrar">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            {{-- CUERPO --}}
            <div class="modal-body">
                {{-- CARGANDO --}}
                <div id="cargandoTrabajador"
                    class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
                    <p class="mt-2 mb-0">
                        Cargando información...
                    </p>
                </div>

                {{-- INFORMACIÓN --}}
                <div id="informacionTrabajador"
                    style="display: none;">

                    <div class="row">
                        {{-- FOTO --}}
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <img id="verFoto"
                                    src=""
                                    alt="Fotografía"
                                    class="img-thumbnail rounded"
                                    style="
                                        width: 180px;
                                        height: 220px;
                                        object-fit: cover;
                                    ">
                            </div>

                            <h5 id="verNombreCompleto"
                                class="font-weight-bold mb-1">
                            </h5>

                            <span id="verCodigo"
                                class="badge badge-primary">
                            </span>
                        </div>

                        {{-- DATOS --}}
                        <div class="col-md-8">
                            <h6 class="text-info font-weight-bold">
                                <i class="fas fa-id-card mr-1"></i>
                                Datos personales
                            </h6>
                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        DNI
                                    </small>
                                    <strong id="verDni">
                                    </strong>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        Sexo
                                    </small>
                                    <strong id="verSexo">
                                    </strong>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        Teléfono
                                    </small>
                                    <strong id="verTelefono">
                                    </strong>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        Fecha de ingreso
                                    </small>
                                    <strong id="verFechaIngreso">
                                    </strong>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <small class="text-muted d-block">
                                        Dirección
                                    </small>
                                    <strong id="verDireccion">
                                    </strong>
                                </div>
                            </div>

                            <h6 class="text-info font-weight-bold mt-2">
                                <i class="fas fa-briefcase mr-1"></i>
                                Información laboral
                            </h6>
                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        Empresa
                                    </small>
                                    <strong id="verEmpresa">
                                    </strong>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        Sucursal
                                    </small>
                                    <strong id="verSucursal">
                                    </strong>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        Área
                                    </small>
                                    <strong id="verArea">
                                    </strong>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        Cargo
                                    </small>
                                    <strong id="verCargo">
                                    </strong>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">
                                        Estado
                                    </small>
                                    <span id="verEstado">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- FOOTER --}}
            <div class="modal-footer bg-light">
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR TRABAJADOR -->
<div class="modal fade"
    id="modalEditarTrabajador"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalEditarTrabajadorLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalEditarTrabajadorLabel">
                    <i class="fas fa-user-edit mr-2"></i>
                    Editar trabajador
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formEditarTrabajador" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="loadingEditarTrabajador"
                        class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-warning"></i>
                        <p class="mt-2 mb-0">
                            Cargando información...
                        </p>
                    </div>

                    <div id="contenidoEditarTrabajador"
                        style="display: none;">
                        <div class="row">
                            <!-- CÓDIGO -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_codigo">
                                        <i class="fas fa-barcode text-primary mr-1"></i>
                                        Código
                                    </label>
                                    <input type="text" id="edit_codigo" class="form-control" readonly>
                                </div>
                            </div>
                            <!-- DNI -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_dni">
                                        <i class="fas fa-id-card text-primary mr-1"></i>
                                        DNI
                                    </label>

                                    <input type="text" name="dni" id="edit_dni" class="form-control" maxlength="8" required>
                                </div>
                            </div>
                            <!-- ESTADO -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_estado">
                                        <i class="fas fa-toggle-on text-primary mr-1"></i>
                                        Estado
                                    </label>
                                    <select name="estado"
                                            id="edit_estado"
                                            class="form-control"
                                            required>
                                        <option value="1">
                                            Activo
                                        </option>
                                        <option value="0">
                                            Inactivo
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-primary">
                            <i class="fas fa-user mr-1"></i>
                            Datos personales
                        </h6>
                        <div class="row">
                            <!-- PRIMER NOMBRE -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_primer_nombre">
                                        Primer nombre
                                    </label>
                                    <input type="text" name="primer_nombre" id="edit_primer_nombre" class="form-control" required>
                                </div>
                            </div>
                            <!-- SEGUNDO NOMBRE -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_segundo_nombre">
                                        Segundo nombre
                                    </label>
                                    <input type="text" name="segundo_nombre" id="edit_segundo_nombre" class="form-control">
                                </div>
                            </div>
                            <!-- PRIMER APELLIDO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_primer_apellido">
                                        Primer apellido
                                    </label>
                                    <input type="text" name="primer_apellido" id="edit_primer_apellido" class="form-control" required>
                                </div>
                            </div>
                            <!-- SEGUNDO APELLIDO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_segundo_apellido">
                                        Segundo apellido
                                    </label>
                                    <input type="text" name="segundo_apellido" id="edit_segundo_apellido" class="form-control" required>
                                </div>
                            </div>

                            <!-- SEXO -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_sexo">
                                        Sexo
                                    </label>
                                    <select name="sexo"
                                            id="edit_sexo"
                                            class="form-control"
                                            required>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                </div>
                            </div>

                            <!-- TELÉFONO -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_telefono">
                                        Teléfono
                                    </label>
                                    <input type="text" name="telefono" id="edit_telefono" class="form-control" maxlength="20">
                                </div>
                            </div>

                            <!-- FECHA INGRESO -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_fecha_ingreso">
                                        Fecha de ingreso
                                    </label>
                                    <input type="date" name="fecha_ingreso" id="edit_fecha_ingreso"
                                        class="form-control" required>
                                </div>
                            </div>

                            <!-- DIRECCIÓN -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_direccion">
                                        Dirección
                                    </label>
                                    <input type="text" name="direccion" id="edit_direccion" class="form-control">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6 class="text-primary">
                            <i class="fas fa-building mr-1"></i>
                            Datos laborales
                        </h6>

                        <div class="row">
                            <!-- EMPRESA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_empresa_id">
                                        Empresa
                                    </label>
                                    <select name="empresa_id"
                                            id="edit_empresa_id"
                                            class="form-control"
                                            required>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">
                                                {{ $empresa->razon_social }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- SUCURSAL -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_sucursal_id">
                                        Sucursal
                                    </label>
                                    <select name="sucursal_id"
                                            id="edit_sucursal_id"
                                            class="form-control"
                                            required>
                                        @foreach($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}">
                                                {{ $sucursal->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- ÁREA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_area_id">
                                        Área
                                    </label>
                                    <select name="area_id"
                                            id="edit_area_id"
                                            class="form-control"
                                            required>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}">
                                                    {{ $area->nombre }}
                                                </option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- CARGO -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_cargo_id">
                                        Cargo
                                    </label>
                                    <select name="cargo_id"
                                            id="edit_cargo_id"
                                            class="form-control"
                                            required>

                                        @foreach($cargos as $cargo)
                                            <option value="{{ $cargo->id }}">
                                                {{ $cargo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-warning"
                            id="btnGuardarEdicion">
                        <i class="fas fa-save mr-1"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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

{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

{{-- Botones de exportación --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>

{{-- Excel --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

{{-- PDF --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

{{-- Exportación e impresión --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    //JS para tabla de trabajadores
$(function () {
    // Evitar inicializar la tabla dos veces.
    if ($.fn.DataTable.isDataTable('#tablaTrabajadores')) {
        return;
    }

    const tabla = $('#tablaTrabajadores').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        },

        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'Todos']
        ],

        searching: true,
        ordering: true,
        paging: true,
        info: true,
        autoWidth: false,
        scrollX: true,

        // Numeración dinámica por página y al filtrar.
        columnDefs: [
            {
                targets: 0,
                searchable: false,
                orderable: false
            },
            {
                targets: 8,
                searchable: false,
                orderable: false
            }
        ],

        order: [[2, 'asc']],
        dom:
            "<'row mb-3'<'col-md-6'B><'col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row mt-3'<'col-md-5'l><'col-md-7'p>>" +
            "<'row'<'col-12'i>>",

        buttons: [
            /* {
                extend: 'copyHtml5',
                text: '<i class="fas fa-copy"></i> Copiar',
                className: 'btn btn-secondary btn-sm',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            }, --*/
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-download mr-1"></i> Descargar',
                className: 'btn btn-success btn-sm',
                title: 'Listado de trabajadores',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7]}
            },
            /* {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                title: 'Listado de trabajadores',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            }, 
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Listado de trabajadores',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            },---*/
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-1"></i> Imprimir',
                className: 'btn btn-primary btn-sm',
                title: 'Listado de trabajadores',
                exportOptions: {columns: [1, 2, 3, 4, 5, 6, 7]}
            }
        ],

        drawCallback: function () {
            const api = this.api();
            const inicio = api.page.info().start;

            api.column(0, { page: 'current' }).nodes().each(
                function (celda, indice) {
                    celda.innerHTML = inicio + indice + 1;
                }
            );
        },

    initComplete: function () {

    const buscador =
        $('#tablaTrabajadores_filter input');

    buscador
        .attr(
            'placeholder',
            'Buscar por nombre, apellido, código o DNI...'
        )
        .addClass('form-control')
        .removeClass('form-control-sm');

        $('#tablaTrabajadores_filter label')
            .contents()
            .filter(function () {
                return this.nodeType === 3;
            })
            .remove();

        $('#tablaTrabajadores_length select')
            .addClass('form-control form-control-sm');

    }
    });
});
</script>    

<script>
    //filtro por sucursales, areas y cargos
$(document).ready(function () {
    /* FILTRAR SUCURSALES, ÁREAS Y CARGOS  */

    $('#empresa_id').on('change', function () {
        let empresaId = $(this).val();
        /* SUCURSALES */
        $('#sucursal_id option').each(function () {
            let opcion = $(this);
            if (!opcion.val()) {
                opcion.show();
                return;
            }

            if (opcion.data('empresa') == empresaId) {
                opcion.show();
            } else {
                opcion.hide();
            }
        });

        $('#sucursal_id').val('');

        /* ÁREAS */
        $('#area_id option').each(function () {
            let opcion = $(this);
            if (!opcion.val()) {
                opcion.show();
                return;
            }

            if (opcion.data('empresa') == empresaId) {
                opcion.show();
            } else {
                opcion.hide();
            }
        });
        $('#area_id').val('');

        /* CARGOS */

        $('#cargo_id option').each(function () {
            let opcion = $(this);
            if (!opcion.val()) {
                opcion.show();
                return;
            }

            if (opcion.data('empresa') == empresaId) {
                opcion.show();
            } else {
                opcion.hide();
            }
        });
        $('#cargo_id').val('');
    });

    /* MOSTRAR NOMBRE DE LA FOTO */

    $('#foto').on('change', function () {
        let nombre = $(this).val().split('\\').pop();

        if (nombre) {
            $(this)
                .next('.custom-file-label')
                .html(nombre);
        }
    });

    /* SOLO NÚMEROS PARA DNI */
    $('#dni').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    /* MAYÚSCULAS */
    $('#primer_nombre, #segundo_nombre, #primer_apellido, #segundo_apellido, #direccion').on('input', function () {
        this.value = this.value.toUpperCase();
    });

    /* LIMPIAR MODAL AL CERRAR */
    $('#modalTrabajador').on('hidden.bs.modal', function () {
        let formulario = $(this).find('form')[0];

        formulario.reset();
        $('#fecha_ingreso').val(
            new Date().toISOString().split('T')[0]
        );

        $('#sucursal_id option').show();
        $('#area_id option').show();
        $('#cargo_id option').show();
        $('#foto')
            .next('.custom-file-label')
            .html('Seleccionar fotografía');
    });

    /* FOCUS AL ABRIR*/
    $('#modalTrabajador').on('shown.bs.modal', function () {
        $('#empresa_id').focus();
    });
});
</script>

<script>
    //generar código automáticamente
$(document).ready(function () {
    let timerCodigo = null;
    function generarCodigo() {
        const primerNombre = $('#primer_nombre').val().trim();
        const primerApellido = $('#primer_apellido').val().trim();

        // Si faltan los datos necesarios, limpiamos el código
        if (!primerNombre || !primerApellido) {
            $('#codigo').val('');
            return;
        }
        clearTimeout(timerCodigo);
        timerCodigo = setTimeout(function () {
            $.ajax({
                url: "{{ route('trabajadores.generarCodigo') }}",
                type: "GET",
                data: {
                    primer_nombre: primerNombre,
                    primer_apellido: primerApellido
                },
                success: function (response) {
                    $('#codigo').val(response.codigo);
                },
                error: function (xhr) {
                    console.error(
                        'Error al generar el código:',
                        xhr.responseText
                    );
                    $('#codigo').val('');
                }
            });
        }, 300);
    }
    // Primer nombre
    $('#primer_nombre').on('input', function () {
        generarCodigo();
    });
    // Primer apellido
    $('#primer_apellido').on('input', function () {
        generarCodigo();
    });
});
</script>

<script>
// VER INFORMACIÓN DEL TRABAJADOR

$(document).on('click', '.btn-ver-trabajador', function () {
    const trabajadorId = $(this).data('id');
    $('#modalVerTrabajador').modal('show');
    $('#cargandoTrabajador').show();
    $('#informacionTrabajador').hide();
    $.ajax({
        url: "{{ url('trabajadores') }}/" + trabajadorId,
        type: "GET",
        success: function (data) {
                let nombreCompleto = data.primer_nombre +
                    (data.segundo_nombre ? ' ' + data.segundo_nombre : '') +
                ' ' +
                data.primer_apellido +
                ' ' +
                data.segundo_apellido;

            $('#verNombreCompleto').text(nombreCompleto);
            $('#verCodigo').text(data.codigo);
            $('#verDni').text(data.dni);
            $('#verSexo').text(
                    data.sexo === 'M'
                        ? 'Masculino'
                        : 'Femenino'
                );
            $('#verTelefono').text(data.telefono || 'No registrado');
            $('#verDireccion').text(data.direccion || 'No registrada');
            $('#verFechaIngreso').text(data.fecha_ingreso || 'No registrada');
            $('#verEmpresa').text(data.empresa || 'No registrada');
            $('#verSucursal').text(data.sucursal || 'No registrada');
            $('#verArea').text(data.area || 'No registrada');
            $('#verCargo').text(data.cargo || 'No registrado');

            if (data.estado) {
                $('#verEstado')
                    .removeClass('badge-danger')
                    .addClass('badge-success')
                    .text('Activo');
            } else {
                $('#verEstado')
                    .removeClass('badge-success')
                    .addClass('badge-danger')
                    .text('Inactivo');
            }

            if (data.foto) {
                $('#verFoto')
                    .attr('src', data.foto);
            } else {
                $('#verFoto')
                    .attr(
                        'src',
                        "{{ asset('imagen/silueta.png') }}"
                    );
            }
            $('#cargandoTrabajador').hide();
            $('#informacionTrabajador').fadeIn();
        },
        error: function (xhr) {
            console.error(
                'Error al obtener trabajador:',
                xhr.responseText
            );
            $('#cargandoTrabajador').hide();
            $('#informacionTrabajador').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    No se pudo cargar la información
                    del trabajador.
                </div>
            `).show();
        }
    });
});
</script>

<script>
    
// EDITAR TRABAJADOR
$(document).on('click', '.btn-editar-trabajador', function () {
    const trabajadorId = $(this).data('id');

    $('#loadingEditarTrabajador').show();
    $('#contenidoEditarTrabajador').hide();

    $('#modalEditarTrabajador').modal('show');

    $.ajax({
        url: "{{ route('trabajadores.show', ':id') }}"
            .replace(':id', trabajadorId),

        type: 'GET',
        dataType: 'json'

    }).done(function (response) {

        console.log('Trabajador recibido:', response);

        // ================================
        // DATOS GENERALES
        // ================================

        $('#edit_codigo').val(response.codigo);
        $('#edit_dni').val(response.dni);

        // ================================
        // DATOS PERSONALES
        // ================================

        $('#edit_primer_nombre').val(response.primer_nombre);
        $('#edit_segundo_nombre').val(response.segundo_nombre);
        $('#edit_primer_apellido').val(response.primer_apellido);
        $('#edit_segundo_apellido').val(response.segundo_apellido);

        $('#edit_sexo').val(response.sexo);
        $('#edit_telefono').val(response.telefono);
        $('#edit_direccion').val(response.direccion);

        $('#edit_fecha_ingreso').val(response.fecha_ingreso);

        if ($('#edit_fecha_cese').length) {
            $('#edit_fecha_cese').val(response.fecha_cese);
        }

        // ================================
        // ESTADO
        // ================================

        $('#edit_estado').val(
            response.estado == 1 || response.estado === true
                ? '1'
                : '0'
        );

        // ================================
        // DATOS LABORALES
        // ================================

        $('#edit_empresa_id').val(
            String(response.empresa_id)
        );

        $('#edit_sucursal_id').val(
            String(response.sucursal_id)
        );

        $('#edit_area_id').val(
            String(response.area_id)
        );

        $('#edit_cargo_id').val(
            String(response.cargo_id)
        );

        // ================================
        // ACTION DEL FORMULARIO
        // ================================

        $('#formEditarTrabajador').attr(
            'action',
            "{{ url('trabajadores') }}/" + response.id
        );

        // ================================
        // MOSTRAR FORMULARIO
        // ================================

        $('#loadingEditarTrabajador').hide();
        $('#contenidoEditarTrabajador').fadeIn();

    }).fail(function (xhr) {

        console.error(
            'Error al cargar trabajador:',
            xhr.status,
            xhr.responseText
        );

        $('#loadingEditarTrabajador').hide();

        $('#modalEditarTrabajador').modal('hide');

        alert(
            'No se pudo cargar la información del trabajador.'
        );
    });
});
</script>

<script>
    // Convertir automáticamente a mayúsculas los campos de texto
$(document).on(
    'input',
    '#edit_dni, #edit_primer_nombre, #edit_segundo_nombre, #edit_primer_apellido, #edit_segundo_apellido, #edit_telefono, #edit_direccion',
    function () {

        this.value = this.value.toUpperCase();
    }
);
</script>    

@endsection