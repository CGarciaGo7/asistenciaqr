@extends('adminlte::page')

@section('title', 'Detalle del Horario')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>
        <i class="fas fa-clock text-primary"></i>
        Detalle del Horario
    </h1>

    <a href="{{ route('horarios.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Regresar
    </a>
</div>

@stop

@section('content')

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">
            Información del Horario
        </h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Empresa</strong>
                <p>{{ $horario->empresa->nombre_comercial }}</p>
            </div>

            <div class="col-md-6">
                <strong>Nombre del Horario</strong>
                <p>{{ $horario->nombre }}</p>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-3">
                <strong>Tipo de Turno</strong>
                <p>
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
                </p>
            </div>

            <div class="col-md-3">
                <strong>Ingreso 1</strong>
                <p>{{ \Carbon\Carbon::parse($horario->hora_ingreso)->format('H:i') }}</p>
            </div>

            <div class="col-md-3">
                <strong>Salida 1</strong>
                <p>{{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}</p>
            </div>

            <div class="col-md-3">
                <strong>Tolerancia</strong>
                <p>{{ $horario->tolerancia }} minutos</p>
            </div>
        </div>

        @if($horario->tipo_turno=='PARTIDO')

        <hr>

        <div class="row">
            <div class="col-md-6">
                <strong>Ingreso 2</strong>
                <p>{{ \Carbon\Carbon::parse($horario->hora_ingreso2)->format('H:i') }}</p>
            </div>

            <div class="col-md-6">
                <strong>Salida 2</strong>
                <p>{{ \Carbon\Carbon::parse($horario->hora_salida2)->format('H:i') }}</p>
            </div>
        </div>
        @endif

        <hr>
        <div class="row">
            <div class="col-md-4">
                <strong>Refrigerio</strong>
                <p>{{ $horario->refrigerio }} minutos</p>
            </div>

            <div class="col-md-4">
                <strong>Estado</strong>

                <p>
                    @if($horario->estado)
                        <span class="badge badge-success">
                            ACTIVO
                        </span>

                    @else
                        <span class="badge badge-danger">
                            INACTIVO
                        </span>
                    @endif

                </p>
            </div>

            <div class="col-md-4">
                <strong>Fecha de Registro</strong>
                <p>{{ $horario->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @if($horario->observacion)

        <hr>

        <div class="row">
            <div class="col-md-12">
                <strong>Observaciones</strong>
                <div class="border rounded p-3 bg-light">
                    {{ $horario->observacion }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="card-footer text-right">
        <a href="{{ route('horarios.edit',$horario) }}"
           class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>

        <a href="{{ route('horarios.index') }}"
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

@stop