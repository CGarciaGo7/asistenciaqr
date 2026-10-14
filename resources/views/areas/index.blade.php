@extends('adminlte::page')

@section('title', 'Áreas')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>
        <i class="fas fa-folder text-primary"></i>
        Gestión de Áreas
    </h1>

    <a href="{{ route('areas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i>
        Nueva Área
    </a>

</div>

@stop

@section('content')

{{-- Mensajes --}}
@if(session('success'))

<div class="alert alert-success alert-dismissible fade show">

    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

@endif

{{-- Cards --}}

<div class="row">
    <div class="col-md-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $total }}</h3>
                <p>Total de Áreas</p>
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
                <p>Áreas Activas</p>
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
                <p>Áreas Inactivas</p>
            </div>

            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

{{-- Buscador --}}

    <div class="card">
    <div class="card-header">
    
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th width="70">ID</th>
                    <th>Nombre del Área</th>
                    <th>Descripción</th>
                    <th width="120">Estado</th>
                    <th width="180">Acciones</th>
                </tr>
            </thead>

        <tbody>

        @forelse($areas as $area)
        <tr>
            <td>{{ $area->id }}</td>
            <td>{{ $area->nombre }}</td>
            <td>{{ $area->descripcion }}</td>
            <td>
            @if($area->estado)
                <span class="badge bg-success">
                Activo
                </span>
            @else
                <span class="badge bg-danger">
                Inactivo
                </span>
            @endif
        </td>

        <td>
            <a href="{{ route('areas.show',$area) }}" class="btn btn-info btn-sm" title="Ver">
            <i class="fas fa-eye"></i> </a>

            <a href="{{ route('areas.edit',$area) }}" class="btn btn-warning btn-sm" title="Editar">
            <i class="fas fa-edit"></i> </a>

            <form action="{{ route('areas.destroy',$area) }}" method="POST" class="d-inline formEliminar">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" title="Activar / Desactivar">
                    <i class="fas fa-power-off"></i> </button>
            </form>
        </td>
    </tr>
        @empty

        <tr>
            <td colspan="6" class="text-center">
                No existen áreas registradas.
            </td>
        </tr>
        @endforelse

        </tbody>
        </table>
        </div>

        <div class="card-footer">

        {{ $areas->links() }}
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
title:'¿Desea cambiar el estado del área?',
text:'El área será activada o desactivada.',
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