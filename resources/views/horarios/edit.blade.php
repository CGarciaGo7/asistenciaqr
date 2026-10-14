@extends('adminlte::page')

@section('title', 'Editar Horario')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>
        <i class="fas fa-edit text-warning"></i>
        Editar Horario

    </h1>

    <a href="{{ route('horarios.index') }}"
       class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Regresar
    </a>
</div>

@stop


@section('content')

<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">
            Actualizar Información del Horario
        </h3>
    </div>

    <form action="{{ route('horarios.update', $horario) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="card-body">
            @include('horarios._form')
        </div>
    </form>
</div>

@stop