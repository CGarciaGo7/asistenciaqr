@extends('adminlte::page')

@section('title', 'Empresas')

@section('content_header')

<div class="d-flex justify-content-between">

    <h1>
        <i class="fas fa-building text-primary"></i>
        Empresas
    </h1>

    <a href="{{ route('empresas.create') }}"
       class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nueva Empresa
    </a>
</div>

@stop

@section('content')

<div class="card">
    <div class="card-header">
        <form method="GET">
            <div class="row">
                <div class="col-md-10">
                    <input
                        type="text"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        class="form-control"
                        placeholder="Buscar por RUC, Razón Social o Nombre Comercial">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                        Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th width="90">Logo</th>
                    <th>RUC</th>
                    <th>Razón Social</th>
                    <th>Nombre Comercial</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th width="180">Acciones</th>
                </tr>
            </thead>
            <tbody>

                @forelse($empresas as $empresa)

                <tr>
                    <td class="text-center">
                        @if($empresa->logo)
                            <img
                                src="{{ asset('storage/'.$empresa->logo) }}"
                                width="60"
                                class="img-thumbnail">
                        @else
                            <img
                                src="{{ asset('img/logo_empresa.png') }}"
                                width="60"
                                class="img-thumbnail">

                        @endif

                    </td>
                    <td>{{ $empresa->ruc }}</td>
                    <td>{{ $empresa->razon_social }}</td>
                    <td>{{ $empresa->nombre_comercial }}</td>
                    <td>{{ $empresa->telefono }}</td>
                    <td>
                        @if($empresa->estado)
                            <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i>
                                Activo
                            </span>
                            @else
                            <span class="badge bg-danger">
                                <i class="fas fa-ban"></i>
                                Inactivo
                            </span>
                            @endif
                    </td>

                    <td>
                        <a href="{{ route('empresas.show',$empresa) }}"
                        class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('empresas.edit',$empresa) }}"
                        class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form
                            action="{{ route('empresas.destroy',$empresa) }}"
                            method="POST"
                            class="formEliminar d-inline">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-danger">
                            <i class="fas fa-power-off"></i>
                            </button>

                        </form>
                    </td>
                </tr>
                @empty

                <tr>
                    <td colspan="7" class="text-center">
                        No existen registros.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $empresas->links() }}
    </div>
</div>

@stop

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        $('.formEliminar').submit(function(e){
        e.preventDefault();
        let form=this;
        Swal.fire({
        title:'¿Cambiar estado de la empresa?',
        text:'La empresa será activada o desactivada.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonText:'Sí',
        cancelButtonText:'Cancelar'
        }).then((result)=>{
        if(result.isConfirmed){
        form.submit();
        }
        });
        });
    </script>

@stop    