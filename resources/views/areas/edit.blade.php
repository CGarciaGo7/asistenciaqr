@extends('adminlte::page')

@section('title', 'Editar Área')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-edit text-warning"></i>
        Editar Área
    </h1>

    <a href="{{ route('areas.index') }}"
       class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

@stop

@section('content')

<div class="card card-warning card-outline">
    <div class="card-header">
        <h3 class="card-title">
            Actualizar información del área
        </h3>
    </div>

    <form
        action="{{ route('areas.update', $area) }}"
        method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('areas._form')
        </div>
    </form>
</div>

@stop

@section('js')

<script>

$(function(){

    function validarArea(){

        let nombre = $("#nombre").val();

        $.ajax({
            url: "{{ route('areas.validar') }}",
            type: "GET",
            data: {
                nombre: nombre,
                id: "{{ $area->id }}"
            },

            success:function(resp){

                if(resp.existe){
                    $("#mensajeNombre").html(
                        '<span class="text-danger"><i class="fas fa-times-circle"></i> Esta área ya existe.</span>'
                    );
                }else{
                    $("#mensajeNombre").html(
                        '<span class="text-success"><i class="fas fa-check-circle"></i> Nombre disponible.</span>'
                    );
                }
            }
        });
    }
    $("#empresa_id").change(validarArea);
    $("#nombre").keyup(validarArea);
});
</script>

@stop