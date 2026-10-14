@extends('adminlte::page')

@section('title', 'Editar Cargo')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-edit text-warning"></i>
        Editar Cargo
    </h1>

    <a href="{{ route('cargos.index') }}"
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
            Actualizar información del cargo
        </h3>
    </div>

    <form
        action="{{ route('cargos.update', $cargo) }}"
        method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('cargos._form')
        </div>
    </form>
</div>

@stop

@section('js')

<script>

$(function(){

    function validarCargo(){

        let empresa = $("#empresa_id").val();
        let nombre = $("#nombre").val();

        if(empresa=='' || nombre.length<3){
            $("#mensajeNombre").html('');
            return;
        }
        $.ajax({
            url: "{{ route('cargos.validar') }}",
            type: "GET",
            data: {
                empresa_id: empresa,
                nombre: nombre,
                id: "{{ $cargo->id }}"
            },

            success:function(resp){

                if(resp.existe){
                    $("#mensajeNombre").html(
                        '<span class="text-danger"><i class="fas fa-times-circle"></i> Este cargo ya existe.</span>'
                    );
                }else{
                    $("#mensajeNombre").html(
                        '<span class="text-success"><i class="fas fa-check-circle"></i> Nombre disponible.</span>'
                    );
                }
            }
        });
    }
    $("#empresa_id").change(validarCargo);
    $("#nombre").keyup(validarCargo);
});
</script>

@stop