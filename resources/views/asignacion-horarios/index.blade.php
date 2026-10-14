@extends('adminlte::page')

@section('title', 'Asignación de Horarios')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>
        <i class="fas fa-calendar-check text-primary"></i>
        Asignación de Horarios
    </h1>

    <a href="{{ route('asignacion-horarios.create') }}"
        class="btn btn-primary">

        <i class="fas fa-plus-circle"></i>
        Nueva Asignación
    </a>
</div>

@stop

@section('content')

@if(session('success'))

<div class="alert alert-success alert-dismissible fade show">

    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
    <button type="button"
            class="close"
            data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>

@endif

@if(session('error'))

<div class="alert alert-danger alert-dismissible fade show">

    <i class="fas fa-times-circle"></i>
    {{ session('error') }}
    <button type="button"
            class="close"
            data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<div class="card card-outline card-primary">
    <div class="card-header">
        <form method="GET"
              action="{{ route('asignacion-horarios.index') }}">
            <div class="row">
                <div class="col-md-5">
                    <input
                        type="text"
                        name="buscar"
                        class="form-control"
                        placeholder="Buscar por código, DNI o nombre..."
                        value="{{ request('buscar') }}">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="thead-dark">

            <tr>
                <th width="60">ID</th>
                <th>Código</th>
                <th>Trabajador</th>
                <th>Empresa</th>
                <th>Cargo</th>
                <th>Tipo Personal</th>
                <th>Horario</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Estado</th>
                <th width="170">Acciones</th>
            </tr>
            </thead>
            <tbody>

            @forelse($asignaciones as $asignacion)

                <tr>
                    <td>{{ $asignacion->id }}</td>
                    <td>{{ $asignacion->trabajador->codigo }}</td>
                    <td>
                        {{ $asignacion->trabajador->apellidos }}
                        {{ $asignacion->trabajador->nombres }}
                    </td>

                    <td>
                        {{ $asignacion->trabajador->empresa->nombre_comercial }}
                    </td>

                    <td>
                        {{ $asignacion->trabajador->cargo->nombre }}
                    </td>

                <td class="text-center">
                    @php
                        $esRotativo = strtoupper($asignacion->trabajador->area->nombre) == 'VENTAS';
                    @endphp

                        @if($esRotativo)
                            <span class="badge badge-primary">
                                <i class="fas fa-sync-alt"></i>
                                ROTATIVO
                            </span>
                        @else
                            <span class="badge badge-success">
                                <i class="fas fa-user-clock"></i>
                                HORARIO FIJO
                            </span>

                        @endif

                    </td>
                    <td>

                        <span class="badge badge-info">
                            {{ $asignacion->horario->nombre }}
                        </span>
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($asignacion->fecha_inicio)->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ $asignacion->fecha_fin
                            ? \Carbon\Carbon::parse($asignacion->fecha_fin)->format('d/m/Y')
                            : 'VIGENTE'
                        }}
                    </td>

                    <td>

                        @if($asignacion->estado)

                            <span class="badge badge-success">
                                ACTIVO
                            </span>
                        @else
                            <span class="badge badge-danger">
                                INACTIVO
                            </span>
                        @endif
                    </td>

                    <td>

                        <a href="{{ route('asignacion-horarios.show',$asignacion) }}"
                            class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('asignacion-horarios.edit',$asignacion) }}"
                            class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form
                            action="{{ route('asignacion-horarios.destroy',$asignacion) }}"
                            method="POST"
                            class="d-inline formulario-eliminar">

                            @csrf

                            @method('DELETE')

                            @if($asignacion->estado)

                                <button
                                    class="btn btn-danger btn-sm">

                                    <i class="fas fa-toggle-off"></i>

                                </button>

                            @else

                                <button
                                    class="btn btn-success btn-sm">

                                    <i class="fas fa-toggle-on"></i>

                                </button>

                            @endif

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="11"
                        class="text-center">

                        No existen asignaciones registradas.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="card-footer">

        {{ $asignaciones->links() }}

    </div>

</div>

@stop

@push('js')

<script>

$(document).on('submit','.formulario-eliminar',function(e){

    e.preventDefault();

    let form=this;

    Swal.fire({
        title:'¿Cambiar estado?',
        text:'La asignación será activada o desactivada.',
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

<script>
$("#trabajador_id").change(function(){

    let trabajador = $(this).val();

    if(trabajador==""){
        $("#empresa").val('');
        $("#area").val('');
        $("#cargo").val('');
        $("#horario_actual").val('');
        $("#alertaTipoPersonal").hide();
        return;
    }

    $.get(

        "{{ url('trabajadores') }}/"+trabajador+"/informacion",

        function(data){
            $("#empresa").val(data.empresa.nombre_comercial);
            $("#area").val(data.area.nombre);
            $("#cargo").val(data.cargo.nombre);
            if(data.horario){
                $("#horario_actual").val(data.horario.nombre);
            }else{
                $("#horario_actual").val("SIN HORARIO");
            }

            // ===== Tipo de personal =====
            let area = data.area.nombre.toUpperCase();

            if(area=="VENTAS"){
                $("#alertaTipoPersonal").html(`
                    <div class="alert alert-primary">
                        <h5>
                            <i class="fas fa-sync-alt"></i>
                            Personal con horario rotativo
                        </h5>
                        Recuerde que este trabajador deberá cambiar de turno semanalmente.
                    </div>
                `);
            }else{
                $("#alertaTipoPersonal").html(`
                    <div class="alert alert-success">
                        <h5>
                            <i class="fas fa-user-clock"></i>
                            Personal con horario fijo
                        </h5>
                        Los cambios de horario son excepcionales.
                    </div>
                `);
            }
            $("#alertaTipoPersonal").fadeIn();
        }
    );
});
</script>

@endpush