```blade
@extends('adminlte::page')

@section('title', 'Detalle del Área')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>
        <i class="fas fa-sitemap text-primary"></i>
        Detalle del Área
    </h1>

    <a href="{{ route('areas.index') }}"
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
            Información del Área
        </h3>
    </div>


    <div class="card-body">

        <div class="row">

            {{-- Icono --}}
            <div class="col-md-3 text-center">

                <i class="fas fa-sitemap text-primary"
                   style="font-size: 130px;">
                </i>

            </div>


            {{-- Información --}}
            <div class="col-md-9">

                <table class="table table-bordered">

                    <tr>
                        <th width="220">ID</th>
                        <td>{{ $area->id }}</td>
                    </tr>


                    <tr>
                        <th>Nombre del Área</th>
                        <td>{{ $area->nombre }}</td>
                    </tr>


                    <tr>
                        <th>Descripción</th>
                        <td>
                            {{ $area->descripcion ?: 'No registrada' }}
                        </td>
                    </tr>


                    <tr>
                        <th>Estado</th>
                        <td>

                            @if($area->estado)

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
                        <td>
                            {{ $area->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>


                    <tr>
                        <th>Última Actualización</th>
                        <td>
                            {{ $area->updated_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>

                </table>

            </div>

        </div>

    </div>


    <div class="card-footer text-end">

        <a href="{{ route('areas.edit', $area) }}"
           class="btn btn-warning">

            <i class="fas fa-edit"></i>
            Editar

        </a>


        <a href="{{ route('areas.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Volver

        </a>

    </div>

</div>

@stop