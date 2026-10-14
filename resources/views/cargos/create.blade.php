@extends('adminlte::page')

@section('title', 'Nuevo Cargo')

@section('content')

<div class="card">
    <div class="card-header bg-primary">
        <h3>Nuevo Cargo</h3>
    </div>

    <form action="{{ route('cargos.store') }}" method="POST">
        <div class="card-body">
            @include('cargos._form')
        </div>
    </form>
</div>

@stop

@section('js')

<script>

$(document).ready(function(){

    function validarCargo(){
        
        let nombre=$("#nombre").val();
        $.get(
            "{{ url('cargos/validar') }}/"+empresa+"/"+encodeURIComponent(nombre),
            function(data){
                if(data.existe){
                    $("#mensajeNombre").html(
                        '<span class="text-danger"><i class="fas fa-times-circle"></i> Este cargo ya existe para la empresa seleccionada.</span>'
                    );
                }else{
                    $("#mensajeNombre").html(
                        '<span class="text-success"><i class="fas fa-check-circle"></i> Nombre disponible.</span>'
                    );
                }
            }
        );
    }
    $("#nombre").keyup(validarCargo);
});
</script>

@stop