@extends('adminlte::page')

@section('title', 'Detalle de Empresa')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>
        <i class="fas fa-building text-primary"></i>
        Detalle de la Empresa
    </h1>

    <a href="{{ route('empresas.index') }}"
       class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>

        Volver

    </a>

</div>

@stop

@section('content')

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            Información General
        </h3>
    </div>

    <div class="card-body">
        <div class="row">
            <!-- Logo -->
            <div class="col-md-3 text-center">
                @if($empresa->logo)
                    <img
                        src="{{ asset('storage/'.$empresa->logo) }}"
                        class="img-thumbnail shadow"
                        style="width:220px;height:220px;object-fit:contain;">
                @else
                    <img
                        src="{{ asset('img/logo_empresa.png') }}"
                        class="img-thumbnail shadow"
                        style="width:220px;height:220px;object-fit:contain;">
                @endif
            </div>

            <!-- Datos -->
            <div class="col-md-9">
                <table class="table table-bordered">
                    <tr>
                        <th width="220">RUC</th>
                        <td>{{ $empresa->ruc }}</td>
                    </tr>

                    <tr>
                        <th>Razón Social</th>
                        <td>{{ $empresa->razon_social }}</td>
                    </tr>

                    <tr>
                        <th>Nombre Comercial</th>
                        <td>{{ $empresa->nombre_comercial }}</td>
                    </tr>

                    <tr>
                        <th>Dirección</th>
                        <td>{{ $empresa->direccion }}</td>
                    </tr>

                    <tr>
                        <th>Teléfono</th>
                        <td>{{ $empresa->telefono }}</td>
                    </tr>

                    <tr>
                        <th>Correo</th>
                        <td>{{ $empresa->correo }}</td>
                    </tr>

                    <tr>
                        <th>Estado</th>
                        <td>
                            @if($empresa->estado)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i>
                                    Activo
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle"></i>
                                    Inactivo
                                </span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Fecha de Registro</th>
                        <td>{{ $empresa->created_at->format('d/m/Y H:i') }}</td>
                    </tr>

                    <tr>
                        <th>Última Actualización</th>
                        <td>{{ $empresa->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="card-footer text-end">
        <a href="{{ route('empresas.edit',$empresa) }}"
           class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('empresas.index') }}"
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

@stop