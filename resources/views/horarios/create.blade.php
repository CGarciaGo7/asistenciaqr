@extends('adminlte::page')

@section('title', 'Nuevo Horario')

@section('content_header')

<h1>

    <i class="fas fa-clock text-primary"></i>

    Nuevo Horario

</h1>

@stop

@section('content')

<div class="card card-primary">

    <div class="card-header">

        <h3 class="card-title">
            Registrar Horario
        </h3>

    </div>

    <form action="{{ route('horarios.store') }}" method="POST">
        <div class="card-body">
            @include('horarios._form')

        </div>

    </form>

</div>

@stop