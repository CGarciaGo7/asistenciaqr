@extends('adminlte::page')

@section('title', 'Nueva Área')

@section('content')

<div class="card">
    <div class="card-header bg-primary">
        <h3>Nueva Área</h3>
    </div>

    <form action="{{ route('areas.store') }}" method="POST">
        <div class="card-body">
            @include('areas._form')
        </div>
    </form>
</div>

@stop

@section('js')

<script>

$(document).ready(function(){

    function validarArea(){
        
        let nombre=$("#nombre").val();
        
        $.get(
            "{{ url('areas/validar') }}"+encodeURIComponent(nombre),
            function(data){
                if(data.existe){
                    $("#mensajeNombre").html(
                        '<span class="text-danger"><i class="fas fa-times-circle"></i> Esta área ya existe para la empresa seleccionada.</span>'
                    );
                }else{
                    $("#mensajeNombre").html(
                        '<span class="text-success"><i class="fas fa-check-circle"></i> Nombre disponible.</span>'
                    );
                }
            }
        );
    }
    $("#nombre").keyup(validarArea);
});
</script>

@stop