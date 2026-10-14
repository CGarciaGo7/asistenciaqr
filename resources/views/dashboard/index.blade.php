@extends('adminlte::page')

@section('title','Dashboard')

@section('content')

<div class="container-fluid">

    <h3>Bienvenido al Sistema de Asistencia</h3>
    {{ Auth::user()->name }}
   
</div>


@endsection